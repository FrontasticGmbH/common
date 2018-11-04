<?php

namespace Frontastic\Backstage\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Frontastic\Backstage\UserBundle\Domain\User;
use Frontastic\Backstage\UserBundle\Domain\AuthentificationInformation;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('frontastic:user:create')
            ->setDescription('Create a new user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userService = $this->getContainer()->get('Frontastic\Backstage\UserBundle\Domain\UserService');

        if (!$input->hasArgument('password')) {
            $helper = $this->getHelper('question');
            $question = new Question('Enter password for user', 'password');
            $password = $helper->ask($input, $output, $question);
        } else {
            $password = $input->getArgument('password');
        }

        $authentificationInformation = new AuthentificationInformation([
            'email' => $input->getArgument('email'),
            'password' => $password,
        ]);

        if ($userService->exists($authentificationInformation->email)) {
            $output->writeln('<error>This email address already is in use.</error>');
            return;
        }

        $user = new User();
        $user->email = $authentificationInformation->email;
        $user->displayName = substr($user->email, 0, strrpos($user->email, '@'));
        $user->setPassword($authentificationInformation->password);
        $user->confirmed = true;

        $user = $userService->store($user);
    }
}
