<?php

namespace Frontastic\Common\CoreBundle\Domain\Mailer;

use Frontastic\Common\CoreBundle\Domain\Mailer;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Twig\Error\LoaderError;

class SwiftMail extends Mailer
{
    private $mailer;
    private $twig;
    private $sender;

    public function __construct(SymfonyMailer $mailer, Environment $twig, string $sender = 'support@frontastic.io')
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->sender = $sender;
    }

    public function sendToUser($user, string $type, string $subject, array $parameters = array())
    {
        $parameters = array_merge(
            $parameters,
            array(
                'user' => $user,
                'subject' => $subject,
            )
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->sender))
            ->to(new Address($user->email))
            ->subject($subject)
            ->htmlTemplate("Emails/$type.html.twig")
            ->context($parameters);

        $bodyRenderer = new BodyRenderer($this->twig, $parameters);
        $bodyRenderer->render($email);

        $this->mailer->send($email);
    }
}
