<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccountApiController extends Controller
{
    public function addAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get(
            'Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory'
        )->factor($context->project);
        $address = Address::newWithProjectSpecificData($this->getJsonBody($request));
        $account = $accountApi->addAddress($context->session->account, $address);

        return new JsonResponse($account, 200);
    }

    public function updateAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get(
            'Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory'
        )->factor($context->project);
        $address = Address::newWithProjectSpecificData(
            array_diff_key($this->getJsonBody($request), array_flip(['_type']))
        );
        $account = $accountApi->updateAddress($context->session->account, $address);

        return new JsonResponse($account, 200);
    }

    public function removeAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get(
            'Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory'
        )->factor($context->project);
        $address = Address::newWithProjectSpecificData(
            array_diff_key($this->getJsonBody($request), array_flip(['_type']))
        );
        $accountApi->removeAddress($context->session->account, $address->addressId);

        return new JsonResponse([], 200);
    }

    public function setDefaultBillingAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get(
            'Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory'
        )->factor($context->project);
        $address = Address::newWithProjectSpecificData(
            array_diff_key($this->getJsonBody($request), array_flip(['_type']))
        );
        $account = $accountApi->setDefaultBillingAddress($context->session->account, $address->addressId);

        return new JsonResponse($account, 200);
    }

    public function setDefaultShippingAddressAction(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get(
            'Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory'
        )->factor($context->project);
        $address = Address::newWithProjectSpecificData(
            array_diff_key($this->getJsonBody($request), array_flip(['_type']))
        );
        $account = $accountApi->setDefaultShippingAddress($context->session->account, $address->addressId);

        return new JsonResponse($account, 200);
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
