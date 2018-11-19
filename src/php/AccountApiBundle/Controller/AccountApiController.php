<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class AccountApiController extends Controller
{
    public function addAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address($this->getJsonBody($request));
        $address = $accountApi->addAddress($context->session->account->accountId, $address);

        return new JsonResponse($address, 200);
    }

    public function updateAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address(array_diff_key($this->getJsonBody($request), array_flip(['_type'])));
        $address = $accountApi->updateAddress($context->session->account->accountId, $address);

        return new JsonResponse($address, 200);
    }

    public function removeAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address(array_diff_key($this->getJsonBody($request), array_flip(['_type'])));
        $accountApi->removeAddress($context->session->account->accountId, $address->addressId);

        return new JsonResponse([], 200);
    }

    public function setDefaultBillingAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address(array_diff_key($this->getJsonBody($request), array_flip(['_type'])));
        $address = $accountApi->setDefaultBillingAddress($context->session->account->accountId, $address->addressId);

        return new JsonResponse($address, 200);
    }

    public function setDefaultShippingAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address(array_diff_key($this->getJsonBody($request), array_flip(['_type'])));
        $address = $accountApi->setDefaultShippingAddress($context->session->account->accountId, $address->addressId);

        return new JsonResponse($address, 200);
    }

    private function getJsonBody(Request $request): array
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }
}
