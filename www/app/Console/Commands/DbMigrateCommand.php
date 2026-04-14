<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbMigrateCommand extends Command
{
    protected static $defaultName = 'db:migrate';

    protected function configure(): void
    {
        $this
            ->setName('db:migrate')
            ->setDescription('Run all pending database migrations.')
            ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'The environment to run the migrations against', 'development');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = $input->getOption('env');
        $output->writeln("<info>Iniciando Migrations no ambiente: {$env}...</info>");

        $phinxBin = __DIR__ . '/../../../vendor/bin/phinx';
        
        // Build the command
        $command = "{$phinxBin} migrate -e {$env}";
        
        // Open a process and pass the output directly to console
        $process = popen($command . " 2>&1", "r");
        
        while (!feof($process)) {
            $output->write(fread($process, 4096));
        }
        
        pclose($process);

        $output->writeln("\n<info>✓ Migrations concluídas com sucesso.</info>");
        return Command::SUCCESS;
    }
}
