<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Symfony\Component\HttpFoundation\Request;

class RequestVerifier
{
    public function isValid(Request $request, string $secret): bool
    {
        return hash_hmac(
            'sha256',
            $request->headers->get('X-Frontastic-Nonce') . ':' . $request->getContent(),
            $secret
        ) === $request->headers->get('X-Frontastic-Hash');
    }

    public function ensure(Request $request, string $secret)
    {
        if (!$this->isValid($request, $secret)) {
            throw new \DomainException(
                \AppKernel::getDebug() ?
                    "Request did not validate against shared secret: $secret" :
                    "Request did not validate against shared secret."
            );
        }
    }
}
