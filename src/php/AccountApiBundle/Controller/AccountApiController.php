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
    public function addAddress(Request $request, Context $context): JsonResponse
    {
        if (!$context->session->loggedIn) {
            throw new AuthenticationException('Not logged in.');
        }

        $accountApi = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory')->factor($context->customer);
        $address = new Address($this->getJsonBody($request));
        $address = $accountApi->addAddress($context->session->account->accountId, $address);

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
