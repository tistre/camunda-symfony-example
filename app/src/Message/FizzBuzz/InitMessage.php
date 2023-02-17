<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class InitMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    protected string $topic = 'FizzBuzz_Init';
}