<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\BuzzMessage;
use App\Message\FizzBuzz\FizzBuzzMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class BuzzHandler
{
    /**
     * @param BuzzMessage $message
     */
    public function __invoke(BuzzMessage $message): void
    {
        $message->assertRequiredProperties([FizzBuzzMessage::VAR_IS_DIVISIBLE_BY_5]);

        $message->setIsBuzz($message->getIsDivisibleBy5());
    }
}