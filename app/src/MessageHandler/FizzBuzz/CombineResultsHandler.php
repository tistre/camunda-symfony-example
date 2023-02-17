<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\CombineResultsMessage;
use App\Message\FizzBuzz\FizzBuzzMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class CombineResultsHandler
{
    /**
     * @param CombineResultsMessage $message
     */
    public function __invoke(CombineResultsMessage $message): void
    {
        $message->assertRequiredProperties([FizzBuzzMessage::VAR_NUMBER]);

        $isFizz = $message->getIsFizz() ?? false;
        $isBuzz = $message->getIsBuzz() ?? false;

        if (!($isFizz || $isBuzz)) {
            $output = $message->getNumber();
        } else {
            $output = sprintf(
                '%s%s',
                $isFizz ? 'Fizz' : '',
                $isBuzz ? 'Buzz' : ''
            );
        }

        $message->setOutput($output);
    }
}