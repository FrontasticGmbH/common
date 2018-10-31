<?php

namespace Frontastic\Common\AccountApiBundle\Controller;

use Frontastic\Common\AccountApiBundle\Domain\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Common\CoreBundle\Controller\CrudController;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\LineItem;
use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class AccountController extends CrudController
{
    /**
     * @var AccountApi
     */
    protected $accountApi;

    public function getAction(Context $context): array
    {
        return [
            'account' => $this->getAccount($context),
        ];
    }

    public function getOrderAction(Context $context, string $order): array
    {
        $accountApi = $this->getAccountApi($context);
        return [
            'order' => $accountApi->getOrder($order),
        ];
    }

    public function addAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $accountApi = $this->getAccountApi($context);

        $account = $this->getAccount($context);
        $accountApi->startTransaction($account);
        $account = $accountApi->addToAccount(
            $account,
            new LineItem\Variant([
                'variant' => new Variant(['sku' => $payload['variant']['sku']]),
                'custom' => $payload['option'] ?: [],
                'count' => $payload['count']
            ])
        );
        $account = $accountApi->commit();

        return [
            'account' => $account,
        ];
    }

    public function updateLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $accountApi = $this->getAccountApi($context);

        $account = $this->getAccount($context);
        $accountApi->startTransaction($account);
        $account = $accountApi->updateLineItem(
            $account,
            $this->getLineItem($account, $payload['lineItemId']),
            $payload['count']
        );
        $account = $accountApi->commit();

        return [
            'account' => $account,
        ];
    }

    public function removeLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $accountApi = $this->getAccountApi($context);

        $account = $this->getAccount($context);
        $accountApi->startTransaction($account);
        $account = $accountApi->removeLineItem(
            $account,
            $this->getLineItem($account, $payload['lineItemId'])
        );
        $account = $accountApi->commit();

        return [
            'account' => $account,
        ];
    }

    private function getLineItem(Account $account, string $lineItemId): LineItem
    {
        foreach ($account->lineItems as $lineItem) {
            if ($lineItem->lineItemId === $lineItemId) {
                return $lineItem;
            }
        }

        throw new \OutOfBoundsException("Could not find line item with ID $lineItemId");
    }

    public function checkoutAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $accountApi = $this->getAccountApi($context);

        // @TODO:
        // [ ] Create new user, if requested
        // [ ] Register for newsletter if requested

        $account = $this->getAccount($context);
        $accountApi->startTransaction($account);
        $account = $accountApi->setEmail(
            $account,
            $payload['user']['email']
        );
        $account = $accountApi->setShippingAddress(
            $account,
            $payload['shipping']
        );
        $account = $accountApi->setBillingAddress(
            $account,
            $payload['billing'] ?: $payload['shipping']
        );
        $account = $accountApi->setPayment(
            $account,
            new Payment([
                'paymentProvider' => $payload['payment']['provider'],
                'paymentId' => $payload['payment']['id'],
                'amount' => $this->getAccount($context)->sum,
                'currency' => $context->currency
            ])
        );
        $account = $accountApi->commit();

        $order = $accountApi->order($account);

        // @HACK: Regenerate session ID to get a "new" account:
        session_regenerate_id();

        return [
            'order' => $order,
        ];
    }

    protected function getAccountApi(Context $context): AccountApi
    {
        if ($this->accountApi) {
            return $this->accountApi;
        }

        /** @var \Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory $accountApiFactory */
        $accountApiFactory = $this->get('Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory');
        return $this->accountApi = $accountApiFactory->factor($context->customer);
    }

    protected function getAccount(Context $context): Account
    {
        $accountApi = $this->getAccountApi($context);
        if ($context->session->loggedIn) {
            return $accountApi->getForUser($context->session->user);
        } else {
            return $accountApi->getAnonymous(session_id());
        }
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }
}
