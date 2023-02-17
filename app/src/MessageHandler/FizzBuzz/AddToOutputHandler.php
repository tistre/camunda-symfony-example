<?php

namespace App\MessageHandler\FizzBuzz;

use App\Message\FizzBuzz\AddToOutputMessage;
use App\Message\FizzBuzz\FizzBuzzMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class AddToOutputHandler
{
    /**
     * @param AddToOutputMessage $message
     */
    public function __invoke(AddToOutputMessage $message): void
    {
        $message->assertRequiredProperties([FizzBuzzMessage::VAR_CURRENT_NUMBER]);

        $isFizz = $message->getIsFizz() ?? false;
        $isBuzz = $message->getIsBuzz() ?? false;

        $output = $message->getOutput() ?? '';

        if (!empty($output)) {
            $output .= ', ';
        }

        if (!($isFizz || $isBuzz)) {
            $output .= $message->getCurrentNumber();
        } else {
            $output .= sprintf(
                '%s%s',
                $isFizz ? 'Fizz' : '',
                $isBuzz ? 'Buzz' : ''
            );
        }

        $message->setOutput($output);
    }
}