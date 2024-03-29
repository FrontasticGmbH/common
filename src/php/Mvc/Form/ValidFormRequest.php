<?php

namespace Frontastic\Common\Mvc\Form;

use Frontastic\Common\Mvc\FormRequest;

class ValidFormRequest implements FormRequest
{
    /** @var array<string,mixed>|object */
    private $validData;

    /**
     * @param array<string,mixed>|object $validData
     */
    public function __construct($validData)
    {
        $this->validData = $validData;
    }

    /**
     * Attempt to handle a form and return true when handled and data is valid.
     *
     * @param array<string,mixed>|object $bindData
     * @param array<string,mixed>        $options
     *
     * @throws \Frontastic\Common\Mvc\Exception\FormAlreadyHandledException when a form was already bound on this
     *         request before.
     */
    public function handle(string $formType, $bindData = null, array $options = []): bool
    {
        return true;
    }

    /**
     * Use this to retrieve the validated data from the form even when you attached `$bindData`.
     *
     * Only by using this method you can mock the form handling by providing a replacement valid value in tests.
     *
     * @return array<string,mixed>|object
     */
    public function getValidData()
    {
        return $this->validData;
    }

    /**
     * Is the bound form valid?
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * Is the request bound to a form?
     */
    public function isBound(): bool
    {
        return true;
    }

    public function getForm(): \Symfony\Component\Form\FormInterface
    {
        throw new \BadMethodCallException("Not supported in ValidFormRequest");
    }

    /**
     * Create the form view for the handled form.
     *
     * Throws exception when no form was handled yet.
     */
    public function createFormView(): \Symfony\Component\Form\FormView
    {
        return new \Symfony\Component\Form\FormView();
    }
}
