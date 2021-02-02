<?php

namespace Frontastic\Common\CartApiBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\ClassToCatwalkPackageMigrationHandler;

ClassToCatwalkPackageMigrationHandler::handleClass(
    'CartFetcher',
    __NAMESPACE__,
    'Frontastic\Catwalk\FrontendBundle\Controller'
);
