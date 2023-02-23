<?php

namespace App\CamundaTransport\Transport;

use App\CamundaTransport\CamundaTopicList;
use App\CamundaTransport\Message\CamundaExternalTaskMessage;
use App\CamundaTransport\Message\CamundaProcessMessage;
use App\CamundaTransport\Stamp\CamundaWorkerIdStamp;
use Psr\Log\LoggerInterface;
use StrehleDe\CamundaClient\CamundaClient;
use StrehleDe\CamundaClient\CamundaTopic;
use StrehleDe\CamundaClient\CamundaTopicBag;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskCompleteRequest;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskFetchAndLockRequest;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskHandleFailureRequest;
use StrehleDe\CamundaClient\Service\ExternalTask\CamundaExternalTaskService;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;


/**
 * Class CamundaReceiver
 * @package App\CamundaTransport
 */
class CamundaReceiver implements ReceiverInterface
{
    protected string $workerId;


    /**
     * CamundaReceiver constructor.
     * @param CamundaClient $camundaClient
     * @param CamundaTopicList $topicList
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected CamundaClient $camundaClient,
        protected CamundaTopicList $topicList,
        protected LoggerInterface $logger
    )
    {
        $this->workerId = uniqid('worker');
    }


    /**
     * Receives some messages.
     *
     * While this method could return an unlimited number of messages,
     * the intention is that it returns only one, or a "small number"
     * of messages each time. This gives the user more flexibility:
     * they can finish processing the one (or "small number") of messages
     * from this receiver and move on to check other receivers for messages.
     * If this method returns too many messages, it could cause a
     * blocking effect where handling the messages received from one
     * call to get() takes a long time, blocking other receivers from
     * being called.
     *
     * If applicable, the Envelope should contain a TransportMessageIdStamp.
     *
     * If a received message cannot be decoded, the message should not
     * be retried again (e.g. if there's a queue, it should be removed)
     * and a MessageDecodingFailedException should be thrown.
     *
     * @return Envelope[]
     * @throws TransportException If there is an issue communicating with the transport
     *
     */
    public function get(): iterable
    {
        $topics = $this->topicList->getTopics();

        $request = (new CamundaExternalTaskFetchAndLockRequest($this->camundaClient))
            ->setWorkerId($this->workerId)
            ->setMaxTasks(1)
            ->setTopics($topics);

        $externalTaskService = new CamundaExternalTaskService($this->camundaClient);

        $externalTasks = $externalTaskService->fetchAndLock($request)->getExternalTasks();

        if (count($externalTasks) === 0) {
            return [];
        }

        $externalTask = $externalTasks[0];

        $messageClass = $this->topicList->getMessageClassByTopic($externalTask->getTopicName());

        $message = (new $messageClass())
            ->fromExternalTask($externalTask);

        $envelope = (new Envelope($message))
            ->with(new TransportMessageIdStamp($externalTask->getId()))
            ->with(new CamundaWorkerIdStamp($this->workerId));

        return [$envelope];
    }


    /**
     * Acknowledges that the passed message was handled.
     *
     * @param Envelope $envelope
     */
    public function ack(Envelope $envelope): void
    {
        /** @var CamundaProcessMessage $message */
        $message = $envelope->getMessage();

        if (!$message instanceof CamundaExternalTaskMessage) {
            throw new LogicException(sprintf('%s: Can only ack CamundaExternalTaskMessage, got %s',
                __METHOD__, get_class($message)));
        }

        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $request = (new CamundaExternalTaskCompleteRequest($this->camundaClient))
            ->setId($stamp->getId())
            ->setWorkerId($this->workerId)
            ->setVariables($message->getCamundaVariables(true));

        try {
            (new CamundaExternalTaskService($this->camundaClient))->complete($request);
        } catch (\RuntimeException $e) {
            // TODO Check for specific Camunda exceptions - for now, we simply assume that any error is safe to ignore
            // to cope with optimistic locking errors, see https://spiegelgruppe.atlassian.net/browse/PHOTO-1101
            $this->logger->warning(sprintf('%s: Ignoring error %s "%s"', __METHOD__, get_class($e), $e->getMessage()));
        }
    }


    /**
     * Called when handling the message failed and it should not be retried.
     *
     * @param Envelope $envelope
     */
    public function reject(Envelope $envelope): void
    {
        $message = $envelope->getMessage();

        if (!$message instanceof CamundaExternalTaskMessage) {
            throw new LogicException(sprintf('%s: Can only reject CamundaExternalTaskMessage, got %s',
                __METHOD__, get_class($message)));
        }

        $stamp = $envelope->last(TransportMessageIdStamp::class);

        if (!$stamp instanceof TransportMessageIdStamp) {
            throw new LogicException('No TransportMessageIdStamp found on the Envelope.');
        }

        $request = (new CamundaExternalTaskHandleFailureRequest($this->camundaClient))
            ->setId($stamp->getId())
            ->setWorkerId($this->workerId)
            ->setErrorMessage($message->getErrorMessage())
            ->setErrorDetails($message->getErrorDetails());

        (new CamundaExternalTaskService($this->camundaClient))->handleFailure($request);
    }
}