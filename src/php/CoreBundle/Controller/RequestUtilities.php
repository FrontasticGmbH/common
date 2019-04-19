<?php

namespace Frontastic\Common\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Request;

class RequestUtilities
{
    public static function prefersHtml(Request $request): bool
    {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('accept'));

        foreach ($acceptHeader->all() as $headerItem) {
            if (strpos($headerItem->getValue(), 'html') !== false) {
                return true;
            }
        }

        return false;
    }
}
