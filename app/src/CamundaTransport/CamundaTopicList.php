<?php

namespace App\CamundaTransport;

use App\CamundaTransport\Message\CamundaProcessMessage;
use StrehleDe\CamundaClient\CamundaTopic;
use StrehleDe\CamundaClient\CamundaTopicBag;


class CamundaTopicList
{
    protected CamundaTopicBag $topics;
    protected array $classByTopicName = [];


    public function __construct()
    {
        $this->topics = new CamundaTopicBag();
    }


    public function addTopic(CamundaProcessMessage $message): void
    {
        if (empty($message->getTopicName())) {
            return;
        }

        $this->topics[] = (new CamundaTopic())
            ->setTopicName($message->getTopicName())
            ->setLockDuration($message->getLockDuration());

        $this->classByTopicName[$message->getTopicName()] = get_class($message);
    }


    /**
     * @return CamundaTopicBag
     */
    public function getTopics(): CamundaTopicBag
    {
        return $this->topics;
    }


    /**
     * @param string $topicName
     * @return string
     */
    public function getMessageClassByTopic(string $topicName): string
    {
        return $this->classByTopicName[$topicName];
    }
}