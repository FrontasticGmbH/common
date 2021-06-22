<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

interface Source
{
    /**
     * Return a sequence of max $count updates since the last revision $since.
     */
    public function updates(string $since, int $count): array;
}
