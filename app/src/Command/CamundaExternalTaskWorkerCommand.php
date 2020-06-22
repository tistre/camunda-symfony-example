<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use StrehleDe\CamundaClient\CamundaClient;
use StrehleDe\CamundaClient\CamundaExternalTaskHandler;
use StrehleDe\CamundaClient\CamundaExternalTaskWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class WorkerCommand
 * @package App\Command
 */
abstract class CamundaExternalTaskWorkerCommand extends Command
{
    /**
     * How many external tasks to fetch and lock
     */
    const FETCH_MAX_TASKS = 1;

    /**
     * How many microseconds to sleep between "fetch and lock" iterations
     */
    const SLEEP_MICROSECONDS_AFTER_FETCH = 100000;

    /**
     * How many external tasks to handle (0 = unlimited)
     * @var int
     */
    protected int $processMaxNum = 0;

    /**
     * How many external tasks were handled so far
     * @var int
     */
    protected int $numProcessed = 0;

    protected CamundaClient $camundaClient;
    protected LoggerInterface $logger;


    /**
     * WorkerCommand constructor.
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

        $this->logger = $logger;
        $this->camundaClient = $camundaClient;
    }


    /**
     * Symfony Command configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Work on external Camunda tasks')
            ->setHelp('In an endless loop, fetch external tasks from Camunda and handle them.')
            ->addOption('num', null, InputOption::VALUE_REQUIRED, 'Maximum number of external tasks to handle', 0)
            ->addOption('topic', null, InputOption::VALUE_IS_ARRAY|InputOption::VALUE_REQUIRED, 'Handle only tasks with this topic', '');
    }


    /**
     * Symfony Command execution
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workerId = $this->getWorkerId();
        $this->processMaxNum = intval($input->getOption('num'));

        $handler = $this->getCamundaExternalTaskHandler();

        $worker = new CamundaExternalTaskWorker($this->camundaClient, $this->logger, $handler, $workerId);

        $filterTopics = $input->getOption('topic');
        $topicNames = $handler->getHandledTopics()->getTopicNames();

        if (count($filterTopics) > 0) {
            $topicNames = $filterTopics;
        }

        $this->logger->info(sprintf(
            '%s: Starting %s with <%s> handling external tasks with the topics <%s> (worker ID <%s>)',
            get_class($this),
            ($this->processMaxNum === 0 ? 'endless loop' : 'to handle ' . $this->processMaxNum . ' task(s)'),
            get_class($handler),
            implode(', ', $topicNames),
            $workerId
        ));

        while (true) {
            foreach ($worker->fetchExternalTasks(self::FETCH_MAX_TASKS, $filterTopics) as $externalTask) {
                $worker->handleExternalTask($externalTask);
                $this->numProcessed++;

                if (($this->processMaxNum > 0) && ($this->numProcessed >= $this->processMaxNum)) {
                    return 0;
                }
            }

            usleep(self::SLEEP_MICROSECONDS_AFTER_FETCH);
        }

        return 0;
    }


    /**
     * Get worker ID for Camunda
     *
     * @return string
     */
    protected function getWorkerId(): string
    {
        return sprintf(
            '%s-%s-%s',
            strtr(get_class($this), ['\\' => '_']),
            php_uname('n'),
            getmypid()
        );
    }


    /**
     * Get handler object from extending class
     *
     * @return CamundaExternalTaskHandler
     */
    abstract protected function getCamundaExternalTaskHandler(): CamundaExternalTaskHandler;
}