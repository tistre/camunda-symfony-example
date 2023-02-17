<?php

namespace App\CamundaTransport;

use App\CamundaTransport\Message\CamundaProcessMessage;


class CamundaTopicList
{
    protected array $topics = [];


    public function addTopic(CamundaProcessMessage $message): void
    {
        if ($message->getTopic() !== null) {
            $this->topics[$message->getTopic()] = [
                'class' => get_class($message),
                'topic' => $message->getTopic()
            ];
        }
    }


    /**
     * @return array
     */
    public function getTopics(): array
    {
        return array_column($this->topics, 'topic');
    }


    /**
     * @param string $topicName
     * @return string
     */
    public function getMessageClassByTopic(string $topicName): string
    {
        return $this->topics[$topicName]['class'];
    }
}