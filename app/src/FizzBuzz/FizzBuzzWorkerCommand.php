<?php

namespace App\FizzBuzz;

use App\Command\CamundaExternalTaskWorkerCommand;
use StrehleDe\CamundaClient\CamundaExternalTaskHandler;


class FizzBuzzWorkerCommand extends CamundaExternalTaskWorkerCommand
{
    protected static $defaultName = 'app:fizzbuzz-worker';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setDescription('Worker process for Camunda FizzBuzz external tasks');
    }


    /**
     * @inheritDoc
     */
    protected function getCamundaExternalTaskHandler(): CamundaExternalTaskHandler
    {
        return new FizzBuzzExternalTaskHandler($this->logger);
    }
}