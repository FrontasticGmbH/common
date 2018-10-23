<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

interface Source
{
    public function updates(string $since, int $count): array;
}
