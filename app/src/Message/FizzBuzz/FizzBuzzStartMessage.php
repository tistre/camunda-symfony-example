<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaProcessStartMessage;


class FizzBuzzStartMessage extends FizzBuzzMessage implements CamundaProcessStartMessage
{

}