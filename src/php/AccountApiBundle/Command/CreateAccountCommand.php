<?php

namespace Frontastic\Common\AccountApiBundle\Command;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AuthentificationInformation;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateAccountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('frontastic:account:create')
            ->setDescription('Create a new account')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the account.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the account.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountService = $this->getContainer()->get('Frontastic\Common\AccountApiBundle\Domain\AccountService');
        $project = $this->getContainer()->get('Frontastic\Common\ReplicatorBundle\Domain\Project');

        if (!$input->hasArgument('password')) {
            $helper = $this->getHelper('question');
            $question = new Question('Enter password for account', 'password');
            $password = $helper->ask($input, $output, $question);
        } else {
            $password = $input->getArgument('password');
        }

        $authentificationInformation = new AuthentificationInformation([
            'email' => $input->getArgument('email'),
            'password' => $password,
        ]);

        $account = new Account();
        $account->email = $authentificationInformation->email;
        $account->displayName = substr($account->email, 0, strrpos($account->email, '@'));
        $account->setPassword($authentificationInformation->password);
        $account->confirmed = true;

        try {
            $accountService->create($account, null, $project->defaultLanguage);
        } catch (DuplicateAccountException $exception) {
            $output->writeln('<error>This email address already is in use.</error>');
            return 1;
        }

        return 0;
    }
}
