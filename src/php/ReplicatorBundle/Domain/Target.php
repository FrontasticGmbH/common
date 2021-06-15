<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

interface Target
{
    /**
     * Returns the latest sequence revision securely stored in the Target.
     */
    public function lastUpdate(): string;

    /**
     * Store all changes in $updates in the corresponding order. Throw
     * exception if a change cannot be stored.
     */
    public function replicate(array $updates): void;
}
