<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class AddToOutputMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    protected string $topic = 'FizzBuzz_AddToOutput';
}