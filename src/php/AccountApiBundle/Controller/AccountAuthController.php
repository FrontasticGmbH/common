<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\ClassToCatwalkPackageMigrationHandler;

ClassToCatwalkPackageMigrationHandler::handleClass(
    'AccountAuthController',
    __NAMESPACE__,
    'Frontastic\Catwalk\FrontendBundle\Controller'
);
