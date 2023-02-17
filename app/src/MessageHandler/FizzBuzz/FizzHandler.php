<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\FizzBuzzMessage;
use App\Message\FizzBuzz\FizzMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


#[AsMessageHandler]
class FizzHandler
{
    /**
     * @param FizzMessage $message
     */
    public function __invoke(FizzMessage $message): void
    {
        $message->assertRequiredProperties([FizzBuzzMessage::VAR_IS_DIVISIBLE_BY_3]);

        $message->setIsFizz($message->getIsDivisibleBy3());
    }
}