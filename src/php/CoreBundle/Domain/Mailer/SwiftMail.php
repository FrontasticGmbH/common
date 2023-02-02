<?php

namespace Frontastic\Common\CoreBundle\Domain\Mailer;

use App\Domain\CustomerStatusReporter;
use Symfony\Component\Templating\EngineInterface;

use Frontastic\Common\CoreBundle\Domain\Mailer;

class SwiftMail extends Mailer
{
    private $mailer;
    private $twig;
    private $sender;



    public function __construct(\Swift_Mailer $mailer, EngineInterface $twig, string $sender = 'support@frontastic.io')
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

    public function sendToUser(
        $user,
        string $type,
        string $subject,
        array $parameters = array(),
        $ignoreErrors = false
    ) {
        $reporter = new CustomerStatusReporter();
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
        } catch (\InvalidArgumentException $e) {
            // Ignore missing text part
        }
        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            $reporter->reportWarning('There was an error sending an email: ' . $e->getMessage());
            if (!$ignoreErrors) {
                throw $e;

            }
        }
    }

    private function renderView(string $template, array $parameters)
    {
        return $this->twig->render($template, $parameters);
    }
}
