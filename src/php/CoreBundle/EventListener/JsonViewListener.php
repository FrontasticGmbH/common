<?php

namespace Frontastic\Common\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

use Frontastic\Common\JsonSerializer;

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

    public function onKernelView(GetResponseForControllerResultEvent $event)
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

        $event->setResponse(new JsonResponse($this->jsonSerializer->serialize($result)));
    }
}
