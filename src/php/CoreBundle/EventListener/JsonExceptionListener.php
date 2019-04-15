<?php

namespace Frontastic\Common\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Frontastic\Common\CoreBundle\Domain\ErrorResult;
use Frontastic\Common\Translatable;

class JsonExceptionListener
{
    /**
     * @var bool
     */
    private $debug;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $acceptableContentTypes = $event->getRequest()->getAcceptableContentTypes();
        if (!in_array('application/json', $acceptableContentTypes) &&
            !in_array('text/json', $acceptableContentTypes) &&
            !$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $exception = $event->getException();

        $errorData = [
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof Translatable) {
            $errorData['code'] = $exception->getTranslationCode();
            $errorData['parameters'] = $exception->getTranslationParameters();
        }

        if ($this->debug) {
            $errorData['file'] = $exception->getFile();
            $errorData['line'] = $exception->getLine();
            $errorData['stack'] = explode(PHP_EOL, $exception->getTraceAsString());
        }

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $event->setResponse(new JsonResponse(new ErrorResult($errorData), $statusCode));
    }
}
