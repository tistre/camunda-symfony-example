<?php

// Usage: /opt/app/bin/console app:start-fizzbuzz-process --numInstances=10 20

namespace App\Command;

use App\Message\FizzBuzz\FizzBuzzStartMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsCommand(name: 'app:start-fizzbuzz-process')]
class StartFizzBuzzProcessCommand extends Command
{
    public function __construct(
        protected MessageBusInterface $messageBus,
        string $name = null
    )
    {
        parent::__construct($name);
    }


    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Start FizzBuzz process')
            ->setHelp('Starts one or more Camunda process instances of the FizzBuzz process definition')
            ->addOption('numInstances', null, InputOption::VALUE_REQUIRED, 'Number of instances', 1)
            ->addArgument('number', InputArgument::REQUIRED, 'Number, example: 20');
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numInstances = intval($input->getOption('numInstances'));

        for ($i = 1; $i <= $numInstances; $i++) {
            $message = (new FizzBuzzStartMessage())
                ->setNumber(intval($input->getArgument('number')))
                ->setBusinessKey($input->getArgument('number'));

            $this->messageBus->dispatch($message);

            $output->writeln(sprintf(
                '%02d Started <%s> process (number: %d)',
                $i,
                $message->getProcessDefinitionKey(),
                $message->getNumber()
            ));
        }

        return 0;
    }
}