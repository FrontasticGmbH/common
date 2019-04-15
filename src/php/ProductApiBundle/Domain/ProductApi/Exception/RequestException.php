<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception;
use Frontastic\Common\Translatable;

class RequestException extends Exception implements Translatable
{
    /**
     * @var string
     */
    public $translationCode;

    /**
     * @var array
     */
    public $translationParameters = [];

    public function setTranslationData($errorCode, array $parameters = [])
    {
        $this->translationCode = $errorCode;
        $this->translationParameters = $parameters;
    }

    public function getTranslationCode(): string
    {
        return 'commercetools.' . $this->translationCode;
    }

    public function getTranslationParameters(): object
    {
        return (object) $this->translationParameters;
    }
}
