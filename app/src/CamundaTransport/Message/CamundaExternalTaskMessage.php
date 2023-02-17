<?php

namespace App\CamundaTransport\Message;


/**
 * Marker interface for an External Task message
 * @package App\CamundaTransport\Message
 */
interface CamundaExternalTaskMessage
{
    public function getErrorMessage(): string;


    public function setErrorMessage(string $message): void;


    public function getErrorDetails(): string;


    public function setErrorDetails(string $details): void;
}