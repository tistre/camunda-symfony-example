<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaExternalTaskMessage;


class CombineResultsMessage extends FizzBuzzMessage implements CamundaExternalTaskMessage
{
    protected string $topic = 'FizzBuzz_CombineResults';
}