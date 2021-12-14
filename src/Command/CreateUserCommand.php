<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-user ',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Crée un utilisateur en base de données.')
            ->addArgument('username', InputArgument::REQUIRED, "Nom d'utilisateur")
            ->addArgument('email', InputArgument::REQUIRED, "Email de l'utilisateur")
            ->addArgument('password', InputArgument::REQUIRED, "Mot de passe en clair de l'utilisateur")
            ->addArgument('role', InputArgument::REQUIRED, "Role de l'utilisateur")
            ->addArgument('fullname', InputArgument::REQUIRED, "Nom complet de l'utilisateur")
            ->addArgument('isVerified', InputArgument::REQUIRED, 'Statut du compte')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output); // TODO: Change the autogenerated stub
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
