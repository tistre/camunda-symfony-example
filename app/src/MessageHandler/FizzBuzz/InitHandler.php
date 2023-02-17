<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\InitMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class InitHandler
{
    /**
     * @param InitMessage $message
     */
    public function __invoke(InitMessage $message): void
    {
        $message->setCurrentNumber(1);

        if ($message->getDoReview() === null) {
            $message->setDoReview(false);
        }
    }
}