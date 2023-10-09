<?php

namespace Frontastic\Common\MvcBundle\Controller\ResultConverter;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Frontastic\Common\MvcBundle\View\TemplateGuesser;
use Frontastic\Common\Mvc\TemplateView;
use Frontastic\Common\Mvc\ViewStruct;
use Twig\Environment;

/**
 * Convert array or {@link TemplateView} struct into templated response.
 *
 * Guess the template names with the same algorithm that @Template()
 * in Sensio's FrameworkExtraBundle uses.
 */
class ArrayToTemplateResponseConverter implements ControllerResultConverter
{
    private $twig;
    private $guesser;
    private $engine;

    public function __construct(Environment $twig, TemplateGuesser $guesser, string $engine)
    {
        $this->twig = $twig;
        $this->guesser = $guesser;
        $this->engine = $engine;
    }

    /**
     * @param mixed $result
     */
    public function supports($result): bool
    {
        return is_array($result) || $result instanceof TemplateView || $result instanceof ViewStruct;
    }

    /**
     * @param mixed $result
     */
    public function convert($result, Request $request): Response
    {
        $controller = (string) $request->attributes->get('_controller');

        if (is_array($result) || $result instanceof ViewStruct) {
            $result = new TemplateView($result);
        } elseif (! ($result instanceof TemplateView)) {
            throw new \RuntimeException(
                sprintf(
                    'Result must be array or TemplateView, %s given',
                    is_object($result) ? get_class($result) : gettype($result)
                )
            );
        }

        return $this->makeResponseFor(
            $controller,
            $result,
            $request->getRequestFormat() ?: 'html'
        );
    }

    private function makeResponseFor(string $controller, TemplateView $templateView, string $requestFormat): Response
    {
        $viewName = $this->guesser->guessControllerTemplateName(
            $controller,
            $templateView->getActionTemplateName(),
            $requestFormat,
            $this->engine
        );

        return new Response(
            $this->twig->render($viewName, $templateView->getViewParams()),
            $templateView->getStatusCode(),
            $templateView->getHeaders()
        );
    }
}
