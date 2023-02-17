<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\IncreaseNumberMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class IncreaseNumberHandler
{
    /**
     * @param IncreaseNumberMessage $message
     */
    public function __invoke(IncreaseNumberMessage $message): void
    {
        $message->setCurrentNumber($message->getCurrentNumber() + 1);
    }
}