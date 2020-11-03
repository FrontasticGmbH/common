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
            $jsonContent = trim($response->getContent());
            if ($jsonContent[0] === '{') {
                $content = json_decode($jsonContent, true);
                $content['__DEBUG'] = $this->serializer->serialize(Debugger::getMessages());
                $response->setContent(json_encode($content));
            }
        }

        if (($headers->has('Content-Type') && preg_match('(html)', $headers->get('Content-Type')))) {
            $content = $response->getContent();
            $debugProp = 'data-debug="' .
                htmlspecialchars(addcslashes(
                    json_encode(
                        $this->serializer->serialize(Debugger::getMessages()),
                        JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT
                    ),
                    '\\'
                )) .
                '"';
            $content = preg_replace('(<\s*div\s*id="appData")i', '\\0 ' . $debugProp, $content);
            $response->setContent($content);
        }
    }
}
