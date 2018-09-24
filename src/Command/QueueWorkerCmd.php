<?php

namespace Hunter\queue\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Hunter\queue\Plugin\ProviderManager;

/**
 * 执行单个队列任务
 * php hunter queue:work
 */
class QueueWorkerCmd extends BaseCommand {
    /**
     * {@inheritdoc}
     */
    protected function configure() {
       $this->setName('queue:work')
            ->setDescription('Process the next job on a queue')
            ->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on')
            ->addOption('daemon', null, InputOption::VALUE_NONE, 'Run the worker in daemon mode')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0)
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force the worker to run even in maintenance mode')
            ->addOption('memory', null, InputOption::VALUE_OPTIONAL, 'The memory limit in megabytes', 128)
            ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3)
            ->addOption('tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
      $parms = $input->getOptions();

      $providerManager = new ProviderManager();
      $provider = $providerManager->loadProvider();
      $output = $provider->receiveItem($parms);

      $output->writeln('['.date("Y-m-d H:i:s").'] '.$output);
    }

}
