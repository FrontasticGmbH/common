<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Customer;

interface ContentApiFactory
{
    public function factor(Customer $customer): ContentApi;
}
