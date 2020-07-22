<?php

namespace Frontastic\Common\SprykerBundle\Domain\Account;

class SalutationHelper
{
    private const GENDER_MALE = 'Male';
    private const GENDER_FEMALE = 'Female';
    private const GENDER_OTHER = 'Other';

    public const DEFAULT_SPRYKER_SALUTATION = 'Mrs';
    public const DEFAULT_FRONTASTIC_SALUTATION = self::DEFAULT_SPRYKER_SALUTATION;

    /**
     * @var array Frontastic salutation => Spryker gender
     */
    private static $genderMap = [
        'Mr' => self::GENDER_MALE,
        'Dr' => self::GENDER_OTHER,
        'Mrs' => self::GENDER_FEMALE,
        'Ms' => self::GENDER_FEMALE,
        'Herr' => self::GENDER_MALE,
        'Frau' => self::GENDER_FEMALE,
        'Divers' => self::GENDER_OTHER,
    ];

    /**
     * @deprecated without replacement
     *
     * @param string $frontasticSalutation
     * @return string
     */
    public static function getSprykerSalutation(string $frontasticSalutation): string
    {
        return $frontasticSalutation;
    }

    /**
     * @deprecated without replacement
     *
     * @param string $sprykerSalutation
     * @return string
     */
    public static function getFrontasticSalutation(string $sprykerSalutation): string
    {
        return $sprykerSalutation;
    }

    /**
     * @param string $frontasticSalutation
     * @return string
     */
    public static function resolveGenderFromSalutation(string $frontasticSalutation): string
    {
        return self::$genderMap[$frontasticSalutation] ?? self::GENDER_MALE;
    }
}
