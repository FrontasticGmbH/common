<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    public function getPosixStrings()
    {
        return array(
            ['de', [
                'language' => 'de',
                'territory' => 'DE',
                'currency' => 'EUR',
                'country' => 'Germany',
                'original' => 'de',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['de_DE', [
                'language' => 'de',
                'territory' => 'DE',
                'currency' => 'EUR',
                'country' => 'Germany',
                'original' => 'de_DE',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['de_DE@EUR', [
                'language' => 'de',
                'territory' => 'DE',
                'currency' => 'EUR',
                'country' => 'Germany',
                'original' => 'de_DE@EUR',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['de_DE@euro', [
                'language' => 'de',
                'territory' => 'DE',
                'currency' => 'EUR',
                'country' => 'Germany',
                'original' => 'de_DE@euro',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['de_DE.UTF8@EUR', [
                'language' => 'de',
                'territory' => 'DE',
                'currency' => 'EUR',
                'country' => 'Germany',
                'original' => 'de_DE.UTF8@EUR',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['de_AT', [
                'language' => 'de',
                'territory' => 'AT',
                'currency' => 'EUR',
                'country' => 'Austria',
                'original' => 'de_AT',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
            ['en_GB@EUR', [
                'language' => 'en',
                'territory' => 'GB',
                'currency' => 'EUR',
                'country' => 'United Kingdom',
                'original' => 'en_GB@EUR',
                'rawApiInput' => [],
                'projectSpecificData' => []
            ]],
        );
    }

    /**
     * @dataProvider getPosixStrings
     */
    public function testCreateFromPosix(string $input, array $expectation)
    {
        $locale = Locale::createFromPosix($input);
        $this->assertEquals($expectation, (array) $locale);
    }
}
