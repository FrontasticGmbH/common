<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

class TokenDecoder
{
    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token): array
    {
        //TODO: temp solution. Use proper token decoder.

        list ($header, $payload, $signature) = explode ('.', $token);

        $decode = json_decode(base64_decode($payload), true);

        return $decode;
    }
}
