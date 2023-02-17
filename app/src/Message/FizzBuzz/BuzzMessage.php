<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class BuzzMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    protected string $topic = 'FizzBuzz_Buzz';
}