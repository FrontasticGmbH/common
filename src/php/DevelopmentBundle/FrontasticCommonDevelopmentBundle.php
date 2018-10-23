<?php

namespace Frontastic\Common\DevelopmentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// phpcs:disable -- Shortcut for debugging in API requests
require_once __DIR__ . '/debug.php';
// phpcs:enable

class FrontasticCommonDevelopmentBundle extends Bundle
{
}
