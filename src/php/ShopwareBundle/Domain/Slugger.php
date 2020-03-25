<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Behat\Transliterator\Transliterator;

class Slugger
{
    public static function slugify(string $text): string
    {
        $slug = Transliterator::urlize($text);

        return self::cleanSlug($slug);
    }

    public static function cleanSlug(string $slug): string
    {
        $slug = preg_replace('@/+-+@', '/', $slug);
        $slug = preg_replace('@-+/+@', '/', $slug);
        $slug = preg_replace('@-+@', '-', $slug);

        return trim($slug, '-');
    }
}
