<?php

namespace Frontastic\Common\SprykerBundle\Domain;

abstract class AbstractRequestData
{
    /**
     * @return array
     */
    abstract protected function getAttributes(): array;

    /**
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * @return string
     */
    public function encode(): string
    {
        $data = [
            'data' => [
                'type' => $this->getType(),
                'attributes' => $this->getAttributes()
            ]
        ];

        return json_encode($data);
    }
}
