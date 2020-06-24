<?php declare(strict_types=1);

namespace Frontastic\Common\SprykerBundle\Domain;

interface MapperInterface
{
    /**
     * @param $resource
     *
     * @return mixed
     */
    public function mapResource($resource);

    /**
     * @return string
     */
    public function getName(): string;
}
