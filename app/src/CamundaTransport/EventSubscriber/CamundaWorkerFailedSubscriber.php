<?php

namespace App\CamundaTransport\EventSubscriber;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;


class CamundaWorkerFailedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => ['onMessageFailed', 1],
        ];
    }


    /**
     * When an exception was raised during Camunda external task handling, add error information to the message
     * @param WorkerMessageFailedEvent $event
     */
    public function onMessageFailed(WorkerMessageFailedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof CamundaExternalTaskMessage) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof HandlerFailedException) {
            $throwable = $throwable->getNestedExceptions()[0];
        }

        $message->setErrorMessage(trim(sprintf('%s %s', get_class($throwable), $throwable->getMessage())));
        $message->setErrorDetails((string)$throwable);
    }
}
