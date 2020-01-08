<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

abstract class CommercetoolsLocaleCreatorFactory
{
    abstract public function factor(Project $project, Client $client): CommercetoolsLocaleCreator;
}
