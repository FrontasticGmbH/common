<?php

namespace Frontastic\Common\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Frontastic\Common\JsonSerializer;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class JsonViewListener
{
    /**
     * @var \Frontastic\Common\JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param string $engine
     */
    public function __construct(JsonSerializer $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    public function onKernelView(ViewEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_controller')) {
            return;
        }

        $controller = $request->attributes->get('_controller');
        $result = $event->getControllerResult();

        if (!$controller || $result instanceof Response) {
            return;
        }

        $acceptableContentTypes = $event->getRequest()->getAcceptableContentTypes();
        if (!in_array('application/json', $acceptableContentTypes) &&
            !in_array('text/json', $acceptableContentTypes) &&
            !($request->getRequestFormat() === 'json')) {
            return;
        }

        // Since the same views are sometimes rendered as HTML and sometimes as
        // JSON we instruct the browser NOT to cache the JSON. Otherwise it
        // might be shown again if the users goes back to a site…
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $event->setResponse(new JsonResponse($this->jsonSerializer->serialize($result)));
    }
}
