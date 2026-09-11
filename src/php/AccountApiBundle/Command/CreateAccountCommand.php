<?php

namespace Frontastic\Common\AccountApiBundle\Command;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountService;
use Frontastic\Common\AccountApiBundle\Domain\AuthentificationInformation;
use Frontastic\Common\AccountApiBundle\Domain\DuplicateAccountException;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'frontastic:account:create', description: 'Create a new account')]
class CreateAccountCommand extends Command
{
    /**
     * Both services are only defined in applications like the catwalk, not in every
     * kernel registering this bundle — hence the on-invalid="null" wiring in services.xml.
     */
    public function __construct(
        private readonly ?AccountService $accountService = null,
        private readonly ?Project $project = null
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the account.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the account.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->accountService || null === $this->project) {
            $output->writeln('<error>The account service is not available in this application.</error>');
            return 1;
        }

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
            $this->accountService->create($account, null, $this->project->defaultLanguage);
        } catch (DuplicateAccountException $exception) {
            $output->writeln('<error>This email address already is in use.</error>');
            return 1;
        }

        return 0;
    }
}
