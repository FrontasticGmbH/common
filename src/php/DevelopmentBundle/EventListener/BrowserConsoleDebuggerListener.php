<?php

namespace Frontastic\Common\DevelopmentBundle\EventListener;

use Frontastic\Common\DevelopmentBundle\Debugger;

use Frontastic\Common\JsonSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BrowserConsoleDebuggerListener
{
    /**
     * @var \Frontastic\Common\JsonSerializer
     */
    private $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (count(Debugger::getMessages()) === 0) {
            return;
        }

        $response = $event->getResponse();
        $headers = $response->headers;

        if (($headers->has('Content-Type') && $headers->get('Content-Type') === 'application/json')
            || $response instanceof JsonResponse
        ) {
            $content = json_decode($response->getContent(), true);
            $content['__DEBUG'] = $this->serializer->serialize(Debugger::getMessages());
            $response->setContent(json_encode($content));
        }

        // TODO: What about HTML? Should we add a <script>-tag to output log messages?
    }
}
