<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class FizzMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    public const TOPIC_NAME = 'FizzBuzz_Fizz';
}