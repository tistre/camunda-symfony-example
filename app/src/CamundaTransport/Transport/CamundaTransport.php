<?php

namespace App\CamundaTransport\Transport;

use App\CamundaTransport\CamundaTopicList;
use Psr\Log\LoggerInterface;
use StrehleDe\CamundaClient\CamundaClient;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;


/**
 * Class CamundaTransport
 * @package App\CamundaTransport
 */
class CamundaTransport implements TransportInterface
{
    protected CamundaReceiver $receiver;
    protected CamundaSender $sender;


    /**
     * CamundaTransport constructor.
     * @param CamundaClient $camundaClient
     * @param CamundaTopicList $topicList
     */
    public function __construct(
        protected CamundaClient $camundaClient,
        protected CamundaTopicList $topicList,
        protected LoggerInterface $logger
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function get(): iterable
    {
        return ($this->getReceiver())->get();
    }


    /**
     * @inheritDoc
     */
    public function ack(Envelope $envelope): void
    {
        ($this->getReceiver())->ack($envelope);
    }


    /**
     * @inheritDoc
     */
    public function reject(Envelope $envelope): void
    {
        ($this->getReceiver())->reject($envelope);
    }


    /**
     * @inheritDoc
     */
    public function send(Envelope $envelope): Envelope
    {
        return ($this->getSender())->send($envelope);
    }


    /**
     * @return CamundaReceiver
     */
    public function getReceiver(): CamundaReceiver
    {
        if (!isset($this->receiver)) {
            $this->receiver = new CamundaReceiver($this->camundaClient, $this->topicList, $this->logger);
        }

        return $this->receiver;
    }


    /**
     * @return CamundaSender
     */
    public function getSender(): CamundaSender
    {
        if (!isset($this->sender)) {
            $this->sender = new CamundaSender($this->camundaClient, $this->topicList);
        }

        return $this->sender;
    }
}