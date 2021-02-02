<?php

namespace Frontastic\Common\WishlistApiBundle\Controller;

use Frontastic\Common\CoreBundle\Domain\ClassToCatwalkPackageMigrationHandler;

ClassToCatwalkPackageMigrationHandler::handleClass(
    'WishlistController',
    __NAMESPACE__,
    'Frontastic\Catwalk\FrontendBundle\Controller'
);
