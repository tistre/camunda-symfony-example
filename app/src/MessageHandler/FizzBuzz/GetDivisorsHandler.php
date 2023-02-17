<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\FizzBuzzMessage;
use App\Message\FizzBuzz\GetDivisorsMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class GetDivisorsHandler
{
    /**
     * @param GetDivisorsMessage $message
     */
    public function __invoke(GetDivisorsMessage $message): void
    {
        $message->assertRequiredProperties([FizzBuzzMessage::VAR_CURRENT_NUMBER]);

        $message->setIsDivisibleBy3($message->getCurrentNumber() % 3 === 0);
        $message->setIsDivisibleBy5($message->getCurrentNumber() % 5 === 0);

        $message->setIsFizz(false);
        $message->setIsBuzz(false);
    }
}