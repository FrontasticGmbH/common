<?php

namespace Frontastic\Common\ProjectApiBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\ClassToCatwalkPackageMigrationHandler;

ClassToCatwalkPackageMigrationHandler::handleClass(
    'AttributesController',
    __NAMESPACE__,
    'Frontastic\Catwalk\FrontendBundle\Controller'
);
