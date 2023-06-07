<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reset-db',
    description: 'Drop and create database, load fixtures',
)]
class ResetDatabaseCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to drop and create database, load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Drop and create database
        $this->runCommand('doctrine:database:drop', ['--force' => true], $output);
        $this->runCommand('doctrine:database:create', ['--no-interaction' => true], $output);

        // Run theses commands in the terminal because the --no-interaction option doesn't work
        exec('symfony console doctrine:migrations:migrate -n');
        exec('symfony console hautelook:fixtures:load -n');

        $output->writeln('Database has been reset.');

        return Command::SUCCESS;
    }

    private function runCommand(string $command, array $arguments, OutputInterface $output): void
    {
        $command = $this->getApplication()->find($command);
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}
