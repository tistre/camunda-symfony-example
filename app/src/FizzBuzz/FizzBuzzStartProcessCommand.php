<?php

namespace App\FizzBuzz;

use Psr\Log\LoggerInterface;
use StrehleDe\CamundaClient\CamundaClient;
use StrehleDe\CamundaClient\Service\ProcessDefinition\CamundaProcessDefinitionService;
use StrehleDe\CamundaClient\Service\ProcessDefinition\CamundaProcessDefinitionStartInstanceRequest;
use StrehleDe\CamundaClient\Variable\CamundaIntegerVariable;
use StrehleDe\CamundaClient\Variable\CamundaVariableBag;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class FizzBuzzStartProcessCommand extends Command
{
    protected static $defaultName = 'app:fizzbuzz-start-process';
    protected CamundaClient $camundaClient;
    protected LoggerInterface $logger;


    /**
     * FizzBuzzStartProcessCommand constructor.
     * @param CamundaClient $camundaClient
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        CamundaClient $camundaClient,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);

        $this->camundaClient = $camundaClient;
        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Start Camunda FizzBuzz process instances')
            ->setHelp('Starts the specified number of process instances')
            ->addArgument('numbers', InputArgument::REQUIRED, 'How many process instances to start, beginning with number 1');
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = new CamundaProcessDefinitionService($this->camundaClient);

        for ($number = 1; $number <= intval($input->getArgument('numbers')); $number++) {
            $variables = new CamundaVariableBag();
            $variables[FizzBuzzProcessVariables::CAMUNDA_VAR_NUMBER] = new CamundaIntegerVariable($number);

            $request = (new CamundaProcessDefinitionStartInstanceRequest($this->camundaClient))
                ->setKey(FizzBuzzProcessVariables::CAMUNDA_PROCESS_DEFINITION_KEY)
                ->setBusinessKey('number_' . $number)
                ->setVariables($variables);

            $service->startInstance($request);

            $this->logger->info(sprintf(
                'Started <%s> process for number <%d>',
                FizzBuzzProcessVariables::CAMUNDA_PROCESS_DEFINITION_KEY,
                $number
            ));
        }

        return 0;
    }
}