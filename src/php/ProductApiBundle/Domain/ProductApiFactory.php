<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ReplicatorBundle\Domain\Customer;

interface ProductApiFactory
{
    public function factor(Customer $customer): ProductApi;

    public function factorFromConfiguration(array $config): ProductApi;
}
