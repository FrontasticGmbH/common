<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

interface Target
{
    public function lastUpdate(): string;

    public function replicate(array $updates): void;
}
