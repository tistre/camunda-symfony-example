<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class AddToOutputMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    public const TOPIC_NAME = 'FizzBuzz_AddToOutput';
}