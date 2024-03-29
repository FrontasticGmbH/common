<?php

namespace Frontastic\Common\MvcBundle\View;

interface TemplateGuesser
{
    /**
     * Return a template reference for the given controller, format, engine
     */
    public function guessControllerTemplateName(
        string $controller,
        ?string $actionName,
        string $format,
        string $engine
    ): string;
}
