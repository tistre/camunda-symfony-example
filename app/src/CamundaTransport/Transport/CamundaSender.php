<?php

namespace App\CamundaTransport\Transport;

use App\CamundaTransport\CamundaTopicList;
use App\CamundaTransport\Message\CamundaExternalTaskMessage;
use App\CamundaTransport\Message\CamundaProcessMessage;
use App\CamundaTransport\Message\CamundaProcessStartMessage;
use App\CamundaTransport\Stamp\CamundaWorkerIdStamp;
use StrehleDe\CamundaClient\CamundaClient;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskHandleFailureRequest;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskService;
use StrehleDe\CamundaClient\Service\ProcessDefinition\CamundaProcessDefinitionService;
use StrehleDe\CamundaClient\Service\ProcessDefinition\CamundaProcessDefinitionStartInstanceRequest;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;


/**
 * Class CamundaSender
 * @package App\CamundaTransport
 */
class CamundaSender implements SenderInterface
{
    /**
     * CamundaSender constructor.
     * @param CamundaClient $camundaClient
     * @param CamundaTopicList $topicList
     */
    public function __construct(
        protected CamundaClient $camundaClient,
        protected CamundaTopicList $topicList
    )
    {
    }


    /**
     * Sends the given envelope.
     *
     * The sender can read different stamps for transport configuration,
     * like delivery delay.
     *
     * If applicable, the returned Envelope should contain a TransportMessageIdStamp.
     *
     * @param Envelope $envelope
     * @return Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof CamundaProcessStartMessage) {
            return $this->startProcess($envelope);
        } elseif ($message instanceof CamundaExternalTaskMessage) {
            return $this->retryExternalTask($envelope);
        }

        throw new LogicException(sprintf('%s: Can only send CamundaProcessStartMessage or CamundaExternalTaskMessage, got %s',
            __METHOD__, get_class($message)));
    }


    /**
     * @param Envelope $envelope
     * @return Envelope
     */
    protected function startProcess(Envelope $envelope): Envelope
    {
        /** @var CamundaProcessMessage $message */
        $message = $envelope->getMessage();

        if (!$message instanceof CamundaProcessStartMessage) {
            throw new LogicException(sprintf('%s: Can only send CamundaProcessStartMessage, got %s',
                __METHOD__, get_class($message)));
        }

        $request = (new CamundaProcessDefinitionStartInstanceRequest($this->camundaClient))
            ->setKey($message->getProcessDefinitionKey())
            ->setVariables($message->getCamundaVariables());

        if ($message->getBusinessKey() !== null) {
            $request->setBusinessKey($message->getBusinessKey());
        }

        $response = (new CamundaProcessDefinitionService($this->camundaClient))->startInstance($request);

        return $envelope->with(new TransportMessageIdStamp($response->getId()));
    }


    /**
     * @param Envelope $envelope
     * @return Envelope
     */
    protected function retryExternalTask(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();

        if (!$message instanceof CamundaExternalTaskMessage) {
            throw new LogicException(sprintf('%s: Can only send CamundaExternalTaskMessage, got %s',
                __METHOD__, get_class($message)));
        }

        $messageIdStamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$messageIdStamp instanceof TransportMessageIdStamp) {
            throw new LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $workerIdStamp = $envelope->last(CamundaWorkerIdStamp::class);

        if (!$workerIdStamp instanceof CamundaWorkerIdStamp) {
            throw new LogicException('No CamundaWorkerIdStamp found on the Envelope.');
        }

        $request = (new CamundaExternalTaskHandleFailureRequest($this->camundaClient))
            ->setId($messageIdStamp->getId())
            ->setWorkerId($workerIdStamp->getId())
            ->setErrorMessage($message->getErrorMessage())
            ->setErrorDetails($message->getErrorDetails());

        (new CamundaExternalTaskService($this->camundaClient))->handleFailure($request);

        return $envelope;
    }
}