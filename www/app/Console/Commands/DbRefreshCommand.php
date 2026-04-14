<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbRefreshCommand extends Command
{
    protected static $defaultName = 'db:refresh';

    protected function configure(): void
    {
        $this
            ->setName('db:refresh')
            ->setDescription('Rollback all migrations, run migrations again, and seed the database.')
            ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'The environment to run the commands against', 'development')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force operation without confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $env = $input->getOption('env');
        $force = $input->getOption('force');

        if (!$force && $env === 'production') {
            if (!$io->confirm('Você está rodando no ambiente de PRODUÇÃO. Isso derrorá o banco de dados inteiro. Deseja continuar?', false)) {
                $io->warning('Operação abortada pelo usuário.');
                return Command::SUCCESS;
            }
        }

        $phinxBin = __DIR__ . '/../../../vendor/bin/phinx';

        $commands = [
            "Rollback" => "{$phinxBin} rollback -t 0 -e {$env}",
            "Migrate" => "{$phinxBin} migrate -e {$env}",
            "Seed" => "{$phinxBin} seed:run -e {$env}",
        ];

        foreach ($commands as $step => $command) {
            $io->section("=> {$step}...");
            $process = popen($command . " 2>&1", "r");
            
            while (!feof($process)) {
                $output->write(fread($process, 4096));
            }
            pclose($process);
        }

        $io->success('Banco de Dados renovado perfeitamente! (Rollback -> Migrate -> Seed)');
        return Command::SUCCESS;
    }
}
