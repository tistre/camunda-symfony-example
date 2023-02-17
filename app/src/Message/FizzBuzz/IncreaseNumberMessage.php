<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class IncreaseNumberMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    protected string $topic = 'FizzBuzz_IncreaseNumber';
}