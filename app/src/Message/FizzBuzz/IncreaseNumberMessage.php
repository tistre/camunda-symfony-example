<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class IncreaseNumberMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    public const TOPIC_NAME = 'FizzBuzz_IncreaseNumber';
}