<?php

declare(strict_types=1);

namespace WPJarvis\Core\Queue\Console;

use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Queue\Capsule\Manager as QueueManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WorkCommand
 *
 * Command to process queued jobs.
 * Similar to Laravel's `queue:work` command.
 *
 * @package WPJarvis\Core\Queue\Console
 */
class WorkCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('queue:work')
            ->setDescription('Process jobs on the queue')
            ->addArgument('connection', InputArgument::OPTIONAL, 'Queue connection name', 'default')
            ->addArgument('queue', InputArgument::OPTIONAL, 'Queue name', 'default')
            ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'Seconds to sleep when no job is available', 3)
            ->addOption('timeout', null, InputOption::VALUE_OPTIONAL, 'Job timeout in seconds', 60)
            ->addOption('tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before failing', 1);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $input->getArgument('connection');
        $queue = $input->getArgument('queue');

        $sleep = (int)$input->getOption('sleep');
        $timeout = (int)$input->getOption('timeout');
        $tries = (int)$input->getOption('tries');

        /** @var QueueManager $manager */
        $manager = app('queue');

        $worker = new Worker(
            $manager->getQueueManager()->getConnector($connection)->connect($connection),
            app('events'),
            app('exception.handler') ?? null // Optional if you have a global exception handler
        );

        $options = new WorkerOptions();
        $options->sleep = $sleep;
        $options->timeout = $timeout;
        $options->tries = $tries;

        $output->writeln("<info>[WPJarvis]</info> Starting worker on connection: <comment>{$connection}</comment>, queue: <comment>{$queue}</comment>");

        $worker->runNextJob($connection, $queue, $options);

        return Command::SUCCESS;
    }
}
