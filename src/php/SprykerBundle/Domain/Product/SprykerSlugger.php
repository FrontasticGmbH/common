<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Behat\Transliterator\Transliterator;

class SprykerSlugger
{
    /**
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        $slug = Transliterator::urlize($text);

        return self::cleanSlug($slug);
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public static function cleanSlug(string $slug): string
    {
        $slug = preg_replace('@/+-+@', '/', $slug);
        $slug = preg_replace('@-+/+@', '/', $slug);
        $slug = preg_replace('@-+@', '-', $slug);

        return trim($slug, '-');
    }
}
