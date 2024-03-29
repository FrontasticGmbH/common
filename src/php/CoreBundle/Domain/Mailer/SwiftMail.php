<?php

namespace Frontastic\Common\CoreBundle\Domain\Mailer;

use Frontastic\Common\CoreBundle\Domain\Mailer;
use Twig\Environment;
use Twig\Error\LoaderError;

class SwiftMail extends Mailer
{
    private $mailer;
    private $twig;
    private $sender;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, string $sender = 'support@frontastic.io')
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->sender = $sender;

        // Fix stupid swiftmail sendmail configuration which is not possible to
        // configure through bundle.
        if ($this->mailer->getTransport() instanceof \Swift_Transport_SendmailTransport) {
            $this->mailer->getTransport()->setCommand('/usr/sbin/sendmail -t');
        }
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

        $message = new \Swift_Message(
            $subject,
            $this->renderView("Emails/$type.html.twig", $parameters),
            'text/html'
        );
        $message->setFrom($this->sender);
        $message->setTo($user->email);

        try {
            $textPart = $this->renderView("Emails/$type.txt.twig", $parameters);
            $message->addPart($textPart, 'text/plain');
        } catch (\InvalidArgumentException | LoaderError $e) {
            // Ignore missing text part
        }

        $this->mailer->send($message);
    }

    private function renderView(string $template, array $parameters)
    {
        return $this->twig->render($template, $parameters);
    }
}
