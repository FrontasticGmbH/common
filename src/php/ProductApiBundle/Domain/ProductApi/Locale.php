<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Kore\DataObject\DataObject;

/**
 * Simple utility class to handle locales:
 *
 * language[_territory[.codeset]][@modifier]
 *
 * - de_DE
 * - en_GB@euro
 */
class Locale extends DataObject
{
    /**
     * A two or three letter identifier for the language, e.g. fr, de, en …
     *
     * @var string
     */
    public $language;

    /**
     * A two letter identifier for the territory, e.g. CH, DE, FR …
     *
     * @var string
     */
    public $territory;

    /**
     * A human readable country identifier.
     *
     * @var string
     */
    public $country;

    /**
     * A three letter identifier for used currency.
     *
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $original;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s_%s.UTF-8@%s', $this->language, $this->territory, $this->currency);
    }

    /**
     * @var string
     */
    const LOCALE = '(^
        (?P<language>[a-z]{2,})
        (?:_(?P<territory>[A-Z]{2,}))?
        (?:\\.(?P<codeset>[A-Z0-9_+-]+))?
        (?:@(?P<modifier>[A-Za-z]+))?
    $)x';

    /**
     * @var string
     */
    const DEFAULT_CURRENCY = 'EUR';

    /**
     * @var string
     */
    const DEFAULT_COUNTRY = 'n.a.';

    /**
     * Mapping for 249 countries taken from https://gist.github.com/marcusbaguley/304261
     *
     * @var array
     */
    const TERRITORY_TO_CURRENCY = [
        'AD' => 'EUR',
        'AE' => 'AED',
        'AF' => 'AFN',
        'AG' => 'XCD',
        'AI' => 'XCD',
        'AL' => 'ALL',
        'AM' => 'AMD',
        'AN' => 'ANG',
        'AO' => 'AOA',
        'AQ' => 'AQD',
        'AR' => 'ARS',
        'AS' => 'EUR',
        'AT' => 'EUR',
        'AU' => 'AUD',
        'AW' => 'ANG',
        'AX' => 'EUR',
        'AZ' => 'AZN',
        'BA' => 'BAM',
        'BB' => 'BBD',
        'BD' => 'BDT',
        'BE' => 'EUR',
        'BF' => 'XOF',
        'BG' => 'BGN',
        'BH' => 'BHD',
        'BI' => 'BIF',
        'BJ' => 'XOF',
        'BL' => 'EUR',
        'BM' => 'BMD',
        'BN' => 'BND',
        'BO' => 'BOB',
        'BR' => 'BRL',
        'BS' => 'BSD',
        'BT' => 'INR',
        'BV' => 'NOK',
        'BW' => 'BWP',
        'BY' => 'BYR',
        'BZ' => 'BZD',
        'CA' => 'CAD',
        'CC' => 'AUD',
        'CD' => 'CDF',
        'CF' => 'XAF',
        'CG' => 'XAF',
        'CH' => 'CHF',
        'CI' => 'XOF',
        'CK' => 'NZD',
        'CL' => 'CLP',
        'CM' => 'XAF',
        'CN' => 'CNY',
        'CO' => 'COP',
        'CR' => 'CRC',
        'CU' => 'CUP',
        'CV' => 'CVE',
        'CX' => 'AUD',
        'CY' => 'CYP',
        'CZ' => 'CZK',
        'DE' => 'EUR',
        'DJ' => 'DJF',
        'DK' => 'DKK',
        'DM' => 'XCD',
        'DO' => 'DOP',
        'DZ' => 'DZD',
        'EC' => 'ECS',
        'EE' => 'EEK',
        'EG' => 'EGP',
        'EH' => 'MAD',
        'ER' => 'ETB',
        'ES' => 'EUR',
        'ET' => 'ETB',
        'FI' => 'EUR',
        'FJ' => 'FJD',
        'FK' => 'FKP',
        'FM' => 'USD',
        'FO' => 'DKK',
        'FR' => 'EUR',
        'GA' => 'XAF',
        'GB' => 'GBP',
        'GD' => 'XCD',
        'GE' => 'GEL',
        'GF' => 'EUR',
        'GG' => 'GGP',
        'GH' => 'GHS',
        'GI' => 'GIP',
        'GL' => 'DKK',
        'GM' => 'GMD',
        'GN' => 'GNF',
        'GP' => 'EUR',
        'GQ' => 'XAF',
        'GR' => 'EUR',
        'GS' => 'GBP',
        'GT' => 'GTQ',
        'GU' => 'USD',
        'GW' => 'XOF',
        'GY' => 'GYD',
        'HK' => 'HKD',
        'HM' => 'AUD',
        'HN' => 'HNL',
        'HR' => 'HRK',
        'HT' => 'HTG',
        'HU' => 'HUF',
        'ID' => 'IDR',
        'IE' => 'EUR',
        'IL' => 'ILS',
        'IM' => 'GBP',
        'IN' => 'INR',
        'IO' => 'USD',
        'IQ' => 'IQD',
        'IR' => 'IRR',
        'IS' => 'ISK',
        'IT' => 'EUR',
        'JE' => 'GBP',
        'JM' => 'JMD',
        'JO' => 'JOD',
        'JP' => 'JPY',
        'KE' => 'KES',
        'KG' => 'KGS',
        'KH' => 'KHR',
        'KI' => 'AUD',
        'KM' => 'KMF',
        'KN' => 'XCD',
        'KP' => 'KPW',
        'KR' => 'KRW',
        'KW' => 'KWD',
        'KY' => 'KYD',
        'KZ' => 'KZT',
        'LA' => 'LAK',
        'LB' => 'LBP',
        'LC' => 'XCD',
        'LI' => 'CHF',
        'LK' => 'LKR',
        'LR' => 'LRD',
        'LS' => 'LSL',
        'LT' => 'LTL',
        'LU' => 'EUR',
        'LV' => 'LVL',
        'LY' => 'LYD',
        'MA' => 'MAD',
        'MC' => 'EUR',
        'MD' => 'MDL',
        'ME' => 'EUR',
        'MF' => 'ANG',
        'MG' => 'MGA',
        'MH' => 'USD',
        'MK' => 'MKD',
        'ML' => 'XOF',
        'MM' => 'MMK',
        'MN' => 'MNT',
        'MO' => 'MOP',
        'MP' => 'USD',
        'MQ' => 'EUR',
        'MR' => 'MRO',
        'MS' => 'XCD',
        'MT' => 'MTL',
        'MU' => 'MUR',
        'MV' => 'MVR',
        'MW' => 'MWK',
        'MX' => 'MXN',
        'MY' => 'MYR',
        'MZ' => 'MZN',
        'NA' => 'NAD',
        'NC' => 'XPF',
        'NE' => 'XOF',
        'NF' => 'AUD',
        'NG' => 'NGN',
        'NI' => 'NIO',
        'NL' => 'EUR',
        'NO' => 'NOK',
        'NP' => 'NPR',
        'NR' => 'AUD',
        'NU' => 'NZD',
        'NZ' => 'NZD',
        'OM' => 'OMR',
        'PA' => 'PAB',
        'PE' => 'PEN',
        'PF' => 'XPF',
        'PG' => 'PGK',
        'PH' => 'PHP',
        'PK' => 'PKR',
        'PL' => 'PLN',
        'PM' => 'EUR',
        'PN' => 'NZD',
        'PR' => 'USD',
        'PS' => 'JOD',
        'PT' => 'EUR',
        'PW' => 'USD',
        'PY' => 'PYG',
        'QA' => 'QAR',
        'RE' => 'EUR',
        'RO' => 'RON',
        'RS' => 'RSD',
        'RU' => 'RUB',
        'RW' => 'RWF',
        'SA' => 'SAR',
        'SB' => 'SBD',
        'SC' => 'SCR',
        'SD' => 'SDG',
        'SE' => 'SEK',
        'SG' => 'SGD',
        'SH' => 'GBP',
        'SI' => 'EUR',
        'SJ' => 'NOK',
        'SK' => 'SKK',
        'SL' => 'SLL',
        'SM' => 'EUR',
        'SN' => 'XOF',
        'SO' => 'SOS',
        'SR' => 'SRD',
        'ST' => 'STD',
        'SV' => 'SVC',
        'SY' => 'SYP',
        'SZ' => 'SZL',
        'TC' => 'USD',
        'TD' => 'XAF',
        'TF' => 'EUR',
        'TG' => 'XOF',
        'TH' => 'THB',
        'TJ' => 'TJS',
        'TK' => 'NZD',
        'TM' => 'TMT',
        'TN' => 'TND',
        'TO' => 'TOP',
        'TP' => 'IDR',
        'TR' => 'TRY',
        'TT' => 'TTD',
        'TV' => 'AUD',
        'TW' => 'TWD',
        'TZ' => 'TZS',
        'UA' => 'UAH',
        'UG' => 'UGX',
        'UM' => 'USD',
        'USAF' => 'USD',
        'US' => 'USD',
        'UY' => 'UYU',
        'UZ' => 'UZS',
        'VA' => 'EUR',
        'VC' => 'XCD',
        'VE' => 'VEF',
        'VG' => 'USD',
        'VI' => 'USD',
        'VN' => 'VND',
        'VU' => 'VUV',
        'WF' => 'XPF',
        'WS' => 'EUR',
        'YE' => 'YER',
        'YT' => 'EUR',
        'ZA' => 'ZAR',
        'ZM' => 'ZMK',
        'ZW' => 'ZWD',
    ];

    /**
     * Mapping for 249 countries taken from https://gist.github.com/marcusbaguley/304261
     *
     * @var array
     */
    const TERRITORY_TO_COUNTRY = [
        'AD' => 'Andorra',
        'AE' => 'United Arab Emirates',
        'AF' => 'Afghanistan',
        'AG' => 'Antigua and Barbuda',
        'AI' => 'Anguilla',
        'AL' => 'Albania',
        'AM' => 'Armenia',
        'AN' => 'Netherlands Antilles',
        'AO' => 'Angola',
        'AQ' => 'Antarctica',
        'AR' => 'Argentina',
        'AS' => 'American Samoa',
        'AT' => 'Austria',
        'AU' => 'Australian',
        'AW' => 'Aruba',
        'AX' => 'Aland Islands',
        'AZ' => 'Azerbaijan',
        'BA' => 'Bosnia and Herzegovina',
        'BB' => 'Barbados',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BF' => 'Burkina Faso',
        'BG' => 'Bulgaria',
        'BH' => 'Bahrain',
        'BI' => 'Burundi',
        'BJ' => 'Benin',
        'BL' => 'Saint Barthelemy',
        'BM' => 'Bermuda',
        'BN' => 'Brunei Darussalam',
        'BO' => 'Bolivia',
        'BR' => 'Brazil',
        'BS' => 'Bahamas',
        'BT' => 'Bhutan',
        'BV' => 'Bouvet Island',
        'BW' => 'Botswana',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'CA' => 'Canada',
        'CC' => 'Cocos (Keeling) Islands',
        'CD' => 'Congo (Kinshasa)',
        'CF' => 'Central African Republic',
        'CG' => 'Congo Republic of the Democratic',
        'CH' => 'Switzerland',
        'CI' => 'Ivory Coast',
        'CK' => 'Cook Islands',
        'CL' => 'Chile',
        'CM' => 'Cameroon',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CR' => 'Costa Rica',
        'CU' => 'Cuba',
        'CV' => 'Cape Verde',
        'CX' => 'Christmas Island',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DE' => 'Germany',
        'DJ' => 'Djibouti',
        'DK' => 'Denmark',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'DZ' => 'Algeria',
        'EC' => 'Ecuador',
        'EE' => 'Estonia',
        'EG' => 'Egypt',
        'EH' => 'Western Sahara',
        'ER' => 'Eritrea',
        'ES' => 'Spain',
        'ET' => 'Ethiopia',
        'FI' => 'Finland',
        'FJ' => 'Fiji',
        'FK' => 'Falkland Islands (Malvinas)',
        'FM' => 'Micronesia Federated States of',
        'FO' => 'Faroe Islands',
        'FR' => 'France',
        'GA' => 'Gabon',
        'GB' => 'United Kingdom',
        'GD' => 'Grenada',
        'GE' => 'Georgia',
        'GF' => 'French Guiana',
        'GG' => 'Guernsey',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GL' => 'Greenland',
        'GM' => 'Gambia',
        'GN' => 'Guinea',
        'GP' => 'Guadeloupe',
        'GQ' => 'Equatorial Guinea',
        'GR' => 'Greece',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'GT' => 'Guatemala',
        'GU' => 'Guam',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HK' => 'Hong Kong',
        'HM' => 'Heard and Mc Donald Islands',
        'HN' => 'Honduras',
        'HR' => 'Croatia (Hrvatska)',
        'HT' => 'Haiti',
        'HU' => 'Hungary',
        'ID' => 'Indonesia',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IM' => 'Isle of Man',
        'IN' => 'India',
        'IO' => 'British Indian Ocean Territory',
        'IQ' => 'Iraq',
        'IR' => 'Iran (Islamic Republic of)',
        'IS' => 'Iceland',
        'IT' => 'Italy',
        'JE' => 'Jersey',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'KE' => 'Kenya',
        'KG' => 'Kyrgyzstan',
        'KH' => 'Cambodia',
        'KI' => 'Kiribati',
        'KM' => 'Comoros',
        'KN' => 'Saint Kitts',
        'KP' => 'Korea North',
        'KR' => 'Korea South',
        'KW' => 'Kuwait',
        'KY' => 'Cayman Islands',
        'KZ' => 'Kazakhstan',
        'LA' => 'Lao PeopleÕs Democratic Republic',
        'LB' => 'Lebanon',
        'LC' => 'Saint Lucia',
        'LI' => 'Liechtenstein',
        'LK' => 'Sri Lanka',
        'LR' => 'Liberia',
        'LS' => 'Lesotho',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'LY' => 'Libyan Arab Jamahiriya',
        'MA' => 'Morocco',
        'MC' => 'Monaco',
        'MD' => 'Moldova Republic of',
        'ME' => 'Montenegro',
        'MF' => 'Saint Martin (French part)',
        'MG' => 'Madagascar',
        'MH' => 'Marshall Islands',
        'MK' => 'Macedonia',
        'ML' => 'Mali',
        'MM' => 'Myanmar',
        'MN' => 'Mongolia',
        'MO' => 'Macao S.A.R.',
        'MP' => 'Northern Mariana Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MS' => 'Montserrat',
        'MT' => 'Malta',
        'MU' => 'Mauritius',
        'MV' => 'Maldives',
        'MW' => 'Malawi',
        'MX' => 'Mexico',
        'MY' => 'Malaysia',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NC' => 'New Caledonia',
        'NE' => 'Niger',
        'NF' => 'Norfolk Island',
        'NG' => 'Nigeria',
        'NI' => 'Nicaragua',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'NP' => 'Nepal',
        'NR' => 'Nauru',
        'NU' => 'Niue',
        'NZ' => 'New Zealand',
        'OM' => 'Oman',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PF' => 'French Polynesia',
        'PG' => 'Papua New Guinea',
        'PH' => 'Philippines',
        'PK' => 'Pakistan',
        'PL' => 'Poland',
        'PM' => 'Saint Pierre and Miquelon',
        'PN' => 'Pitcairn',
        'PR' => 'Puerto Rico',
        'PS' => 'Palestinian Territory',
        'PT' => 'Portugal',
        'PW' => 'Palau',
        'PY' => 'Paraguay',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RS' => 'Serbia',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SA' => 'Saudi Arabia',
        'SB' => 'Solomon Islands',
        'SC' => 'Seychelles',
        'SD' => 'Sudan',
        'SE' => 'Sweden',
        'SG' => 'Singapore',
        'SH' => 'Saint Helena',
        'SI' => 'Slovenia',
        'SJ' => 'Svalbard and Jan Mayen Islands',
        'SK' => 'Slovakia (Slovak Republic)',
        'SL' => 'Sierra Leone',
        'SM' => 'San Marino',
        'SN' => 'Senegal',
        'SO' => 'Somalia',
        'SR' => 'Suriname',
        'ST' => 'Sao Tome and Principe',
        'SV' => 'El Salvador',
        'SY' => 'Syrian Arab Republic',
        'SZ' => 'Swaziland',
        'TC' => 'Turks and Caicos Islands',
        'TD' => 'Chad',
        'TF' => 'French Southern Territories',
        'TG' => 'Togo',
        'TH' => 'Thailand',
        'TJ' => 'Tajikistan',
        'TK' => 'Tokelau',
        'TM' => 'Turkmenistan',
        'TN' => 'Tunisia',
        'TO' => 'Tonga',
        'TP' => 'East Timor',
        'TR' => 'Turkey',
        'TT' => 'Trinidad and Tobago',
        'TV' => 'Tuvalu',
        'TW' => 'Taiwan',
        'TZ' => 'Tanzania',
        'UA' => 'Ukraine',
        'UG' => 'Uganda',
        'UM' => 'United States Minor Outlying Islands',
        'USAF' => 'US Armed Forces',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VA' => 'Vatican City State (Holy See)',
        'VC' => 'Saint Vincent Grenadines',
        'VE' => 'Venezuela',
        'VG' => 'Virgin Islands (British)',
        'VI' => 'Virgin Islands (US)',
        'VN' => 'Vietnam',
        'VU' => 'Vanuatu',
        'WF' => 'Wallis and Futuna Islands',
        'WS' => 'Samoa',
        'YE' => 'Yemen',
        'YT' => 'Mayotte',
        'ZA' => 'South Africa',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    ];

    private const LANGUAGE_TO_TERRITORY = [
        'en' => 'GB',
    ];

    public static function createFromPosix(string $locale): Locale
    {
        if (0 === preg_match(self::LOCALE, $locale, $matches)) {
            throw new \InvalidArgumentException(
                "The given locale $locale does not match <language[_territory[.codeset]][@modifier]> (en_DE.UTF-8@EUR)"
            );
        }

        $language = $matches['language'];
        $territory = $matches['territory'] ?? self::guessTerritory($language);
        $currency = !empty($matches['modifier']) ?
            self::modifierToCurrency($matches['modifier']):
            self::guessCurrency($territory);

        return new Locale([
            'language' => $language,
            'territory' => $territory,
            'currency' => $currency,
            'country' => self::guessCountry($territory),
            'original' => $locale,
        ]);
    }

    private static function guessTerritory(string $language): string
    {
        return self::LANGUAGE_TO_TERRITORY[\strtolower($language)] ?? strtoupper($language);
    }

    /**
     * @param $modifier
     * @return string
     */
    private static function modifierToCurrency(string $modifier): string
    {
        foreach (self::TERRITORY_TO_CURRENCY as $currency) {
            if (strcasecmp($modifier, $currency) === 0) {
                return $currency;
            }
        }
        switch ($modifier) {
            case 'euro':
                return 'EUR';
        }
        if (in_array($modifier, self::TERRITORY_TO_CURRENCY)) {
            return $modifier;
        }
        throw new \InvalidArgumentException("Unknown currency modifier {$modifier}.");
    }

    /**
     * @param string $territory
     * @return string
     */
    private static function guessCurrency(string $territory): string
    {
        if (isset(self::TERRITORY_TO_CURRENCY[$territory])) {
            return self::TERRITORY_TO_CURRENCY[$territory];
        }
        return self::DEFAULT_CURRENCY;
    }

    /**
     * @param string $territory
     * @return string
     */
    private static function guessCountry(string $territory): string
    {
        if (isset(self::TERRITORY_TO_COUNTRY[$territory])) {
            return self::TERRITORY_TO_COUNTRY[$territory];
        }
        return self::DEFAULT_COUNTRY;
    }
}
