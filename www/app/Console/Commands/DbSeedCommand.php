<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbSeedCommand extends Command
{
    protected static $defaultName = 'db:seed';

    protected function configure(): void
    {
        $this
            ->setName('db:seed')
            ->setDescription('Run the database seeders.')
            ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'The environment to run the seeders against', 'development');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = $input->getOption('env');
        $output->writeln("<info>Iniciando Plantio de Seeders ({$env})...</info>");

        $phinxBin = __DIR__ . '/../../../vendor/bin/phinx';
        
        $command = "{$phinxBin} seed:run -e {$env}";
        
        $process = popen($command . " 2>&1", "r");
        
        while (!feof($process)) {
            $output->write(fread($process, 4096));
        }
        
        pclose($process);

        $output->writeln("\n<info>✓ Seeds plantadas com sucesso.</info>");
        return Command::SUCCESS;
    }
}
