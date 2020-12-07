<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

use Frontastic\Common\CoreBundle\Domain\Json\Json;

class TokenDecoder
{
    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token): array
    {
        //TODO: temp solution. Use proper token decoder.

        list($header, $payload, $signature) = explode('.', $token);

        $decode = Json::decode(base64_decode($payload), true);

        return $decode;
    }
}
