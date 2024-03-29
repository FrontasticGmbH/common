<?php

namespace Frontastic\Common\MvcBundle\Controller\ResultConverter;

use Frontastic\Common\Mvc\RedirectRoute;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RedirectConverter implements ControllerResultConverter
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param mixed $result
     */
    public function supports($result): bool
    {
        return $result instanceof RedirectRoute;
    }

    /**
     * @param mixed $result
     */
    public function convert($result, Request $request): Response
    {
        assert($result instanceof RedirectRoute);

        $response = new Response("", $result->getStatusCode());

        $response->headers->set(
            'Location',
            $this->router->generate(
                $result->getRouteName(),
                $result->getParameters()
            )
        );

        return $response;
    }
}
