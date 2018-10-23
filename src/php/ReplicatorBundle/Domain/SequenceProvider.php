<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

class SequenceProvider
{
    public function get(): string
    {
        list($microseconds, $seconds) = explode(' ', microtime());
        return str_pad($seconds, 12, '0', STR_PAD_LEFT) . substr($microseconds, 2, 6);
    }

    public function next(string $sequence): string
    {
        return str_pad($sequence + 1, 18, '0', STR_PAD_LEFT);
    }
}
