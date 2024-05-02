<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use PHPUnit\Framework\TestCase;

class CustomerServiceTest extends TestCase
{
    public function testGetCustomersWithRealData()
    {
        $customerService = new CustomerService(
            __DIR__ . '/../../../../../../../saas/backstage/config/customers',
            __DIR__ . '/../../../../../../../saas/automation/roles/website/backstage/templates/customer/*.yml'
        );

        $actualCustomers = $customerService->getCustomers();

        $this->assertTrue(count($actualCustomers) > 2, 'Parsed fewer than 2 customers');
        $this->assertCustomerExists('frontastictest', $actualCustomers);
    }

    public function testGetCustomersDemoNotExistsWhenNotDeployed()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_not_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_not_deployed/provisioning/*.yml',
        );

        $actualCustomers = $customerService->getCustomers();

        $this->assertCustomerNotExists('demo', $actualCustomers);
    }

    public function testGetCustomersDemoExistsWhenDeployed()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_deployed/provisioning/*.yml',
        );

        $actualCustomers = $customerService->getCustomers();

        $this->assertCustomerExists('demo', $actualCustomers);
    }

    public function testGetCustomersTestExistsWhenNotDeployed()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_not_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_not_deployed/provisioning/*.yml',
        );

        $actualCustomers = $customerService->getCustomers();

        $this->assertCustomerExists('test', $actualCustomers);
    }

    public function testGetCustomerDeployedSuccess()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_deployed/provisioning/*.yml',
        );

        $customer = $customerService->getCustomer('frontastictest');
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('frontastictest', $customer->name);
    }

    public function testGetCustomerNotDeployedSuccess()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_not_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_not_deployed/provisioning/*.yml',
        );

        $customer = $customerService->getCustomer('frontastictest');
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('frontastictest', $customer->name);
    }

    public function testGetFailure()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_not_deployed/deployed',
            __DIR__ . '/_fixtures/backstage_not_deployed/provisioning/*.yml',
        );

        $this->expectException(\OutOfBoundsException::class);
        $customer = $customerService->getCustomer('idontexist');
    }

    public function testGetCustomerDeployedOverridesProvisioning()
    {
        $customerService = new CustomerService(
            __DIR__ . '/_fixtures/backstage_deployed_difference/deployed',
            __DIR__ . '/_fixtures/backstage_deployed_difference/provisioning/*.yml',
        );

        $customer = $customerService->getCustomer('frontastictest');
        $this->assertEquals('$this_is_deployed', $customer->secret);
    }

    /**
     * @param string $customerName
     * @param Customer[] $actualCustomers
     */
    private function assertCustomerExists(string $customerName, array $actualCustomers)
    {
        foreach ($actualCustomers as $actualCustomer) {
            if ($actualCustomer->name === $customerName) {
                // Ensure an assertion was performed
                $this->assertEquals($customerName, $actualCustomer->name);
                return;
            }
        }
        $this->fail(sprintf('Customer "%s" not found in customer list', $customerName));
    }

    /**
     * @param string $customerName
     * @param Customer[] $actualCustomers
     */
    private function assertCustomerNotExists(string $customerName, array $actualCustomers)
    {
        foreach ($actualCustomers as $actualCustomer) {
            if ($actualCustomer->name === $customerName) {
                $this->fail(sprintf('Customer "%s" found in customer list', $customerName));
            }
        }
        // Ensure an assertion was performed
        $this->assertTrue(true);
    }
}
