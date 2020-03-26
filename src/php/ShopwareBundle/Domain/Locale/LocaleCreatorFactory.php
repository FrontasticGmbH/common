<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\Locale;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProjectApiBundle\Domain\DefaultProjectApiFactory;

class LocaleCreatorFactory
{
    /**
     * @var \Frontastic\Common\ProjectApiBundle\Domain\DefaultProjectApiFactory
     */
    private $projectApiFactory;
    /**
     * @var \Frontastic\Catwalk\ApiCoreBundle\Domain\Context
     */
    private $context;

    public function __construct(DefaultProjectApiFactory $projectApiFactory, Context $context)
    {
        $this->projectApiFactory = $projectApiFactory;
        $this->context = $context;
    }

    public function factor(): LocaleCreator
    {
        return new LocaleCreator(
            $this->projectApiFactory->factor($this->context->project)
        );
    }
}
