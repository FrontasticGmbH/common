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
        if ($this->original) {
            return $this->original;
        }
        return sprintf('%s_%s', $this->language, $this->territory);
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
        'NZ' => 'NZD',
        'CK' => 'NZD',
        'NU' => 'NZD',
        'PN' => 'NZD',
        'TK' => 'NZD',
        'AU' => 'AUD',
        'CX' => 'AUD',
        'CC' => 'AUD',
        'HM' => 'AUD',
        'KI' => 'AUD',
        'NR' => 'AUD',
        'NF' => 'AUD',
        'TV' => 'AUD',
        'AS' => 'EUR',
        'AD' => 'EUR',
        'AT' => 'EUR',
        'BE' => 'EUR',
        'FI' => 'EUR',
        'FR' => 'EUR',
        'GF' => 'EUR',
        'TF' => 'EUR',
        'DE' => 'EUR',
        'GR' => 'EUR',
        'GP' => 'EUR',
        'IE' => 'EUR',
        'IT' => 'EUR',
        'LU' => 'EUR',
        'MQ' => 'EUR',
        'YT' => 'EUR',
        'MC' => 'EUR',
        'NL' => 'EUR',
        'PT' => 'EUR',
        'RE' => 'EUR',
        'WS' => 'EUR',
        'SM' => 'EUR',
        'SI' => 'EUR',
        'ES' => 'EUR',
        'VA' => 'EUR',
        'GS' => 'GBP',
        'GB' => 'GBP',
        'JE' => 'GBP',
        'IO' => 'USD',
        'GU' => 'USD',
        'MH' => 'USD',
        'FM' => 'USD',
        'MP' => 'USD',
        'PW' => 'USD',
        'PR' => 'USD',
        'TC' => 'USD',
        'US' => 'USD',
        'UM' => 'USD',
        'VG' => 'USD',
        'VI' => 'USD',
        'HK' => 'HKD',
        'CA' => 'CAD',
        'JP' => 'JPY',
        'AF' => 'AFN',
        'AL' => 'ALL',
        'DZ' => 'DZD',
        'AI' => 'XCD',
        'AG' => 'XCD',
        'DM' => 'XCD',
        'GD' => 'XCD',
        'MS' => 'XCD',
        'KN' => 'XCD',
        'LC' => 'XCD',
        'VC' => 'XCD',
        'AR' => 'ARS',
        'AM' => 'AMD',
        'AW' => 'ANG',
        'AN' => 'ANG',
        'AZ' => 'AZN',
        'BS' => 'BSD',
        'BH' => 'BHD',
        'BD' => 'BDT',
        'BB' => 'BBD',
        'BY' => 'BYR',
        'BZ' => 'BZD',
        'BJ' => 'XOF',
        'BF' => 'XOF',
        'GW' => 'XOF',
        'CI' => 'XOF',
        'ML' => 'XOF',
        'NE' => 'XOF',
        'SN' => 'XOF',
        'TG' => 'XOF',
        'BM' => 'BMD',
        'BT' => 'INR',
        'IN' => 'INR',
        'BO' => 'BOB',
        'BW' => 'BWP',
        'BV' => 'NOK',
        'NO' => 'NOK',
        'SJ' => 'NOK',
        'BR' => 'BRL',
        'BN' => 'BND',
        'BG' => 'BGN',
        'BI' => 'BIF',
        'KH' => 'KHR',
        'CM' => 'XAF',
        'CF' => 'XAF',
        'TD' => 'XAF',
        'CG' => 'XAF',
        'GQ' => 'XAF',
        'GA' => 'XAF',
        'CV' => 'CVE',
        'KY' => 'KYD',
        'CL' => 'CLP',
        'CN' => 'CNY',
        'CO' => 'COP',
        'KM' => 'KMF',
        'CD' => 'CDF',
        'CR' => 'CRC',
        'HR' => 'HRK',
        'CU' => 'CUP',
        'CY' => 'CYP',
        'CZ' => 'CZK',
        'DK' => 'DKK',
        'FO' => 'DKK',
        'GL' => 'DKK',
        'DJ' => 'DJF',
        'DO' => 'DOP',
        'TP' => 'IDR',
        'ID' => 'IDR',
        'EC' => 'ECS',
        'EG' => 'EGP',
        'SV' => 'SVC',
        'ER' => 'ETB',
        'ET' => 'ETB',
        'EE' => 'EEK',
        'FK' => 'FKP',
        'FJ' => 'FJD',
        'PF' => 'XPF',
        'NC' => 'XPF',
        'WF' => 'XPF',
        'GM' => 'GMD',
        'GE' => 'GEL',
        'GI' => 'GIP',
        'GT' => 'GTQ',
        'GN' => 'GNF',
        'GY' => 'GYD',
        'HT' => 'HTG',
        'HN' => 'HNL',
        'HU' => 'HUF',
        'IS' => 'ISK',
        'IR' => 'IRR',
        'IQ' => 'IQD',
        'IL' => 'ILS',
        'JM' => 'JMD',
        'JO' => 'JOD',
        'KZ' => 'KZT',
        'KE' => 'KES',
        'KP' => 'KPW',
        'KR' => 'KRW',
        'KW' => 'KWD',
        'KG' => 'KGS',
        'LA' => 'LAK',
        'LV' => 'LVL',
        'LB' => 'LBP',
        'LS' => 'LSL',
        'LR' => 'LRD',
        'LY' => 'LYD',
        'LI' => 'CHF',
        'CH' => 'CHF',
        'LT' => 'LTL',
        'MO' => 'MOP',
        'MK' => 'MKD',
        'MG' => 'MGA',
        'MW' => 'MWK',
        'MY' => 'MYR',
        'MV' => 'MVR',
        'MT' => 'MTL',
        'MR' => 'MRO',
        'MU' => 'MUR',
        'MX' => 'MXN',
        'MD' => 'MDL',
        'MN' => 'MNT',
        'MA' => 'MAD',
        'EH' => 'MAD',
        'MZ' => 'MZN',
        'MM' => 'MMK',
        'NA' => 'NAD',
        'NP' => 'NPR',
        'NI' => 'NIO',
        'NG' => 'NGN',
        'OM' => 'OMR',
        'PK' => 'PKR',
        'PA' => 'PAB',
        'PG' => 'PGK',
        'PY' => 'PYG',
        'PE' => 'PEN',
        'PH' => 'PHP',
        'PL' => 'PLN',
        'QA' => 'QAR',
        'RO' => 'RON',
        'RU' => 'RUB',
        'RW' => 'RWF',
        'ST' => 'STD',
        'SA' => 'SAR',
        'SC' => 'SCR',
        'SL' => 'SLL',
        'SG' => 'SGD',
        'SK' => 'SKK',
        'SB' => 'SBD',
        'SO' => 'SOS',
        'ZA' => 'ZAR',
        'LK' => 'LKR',
        'SD' => 'SDG',
        'SR' => 'SRD',
        'SZ' => 'SZL',
        'SE' => 'SEK',
        'SY' => 'SYP',
        'TW' => 'TWD',
        'TJ' => 'TJS',
        'TZ' => 'TZS',
        'TH' => 'THB',
        'TO' => 'TOP',
        'TT' => 'TTD',
        'TN' => 'TND',
        'TR' => 'TRY',
        'TM' => 'TMT',
        'UG' => 'UGX',
        'UA' => 'UAH',
        'AE' => 'AED',
        'UY' => 'UYU',
        'UZ' => 'UZS',
        'VU' => 'VUV',
        'VE' => 'VEF',
        'VN' => 'VND',
        'YE' => 'YER',
        'ZM' => 'ZMK',
        'ZW' => 'ZWD',
        'AX' => 'EUR',
        'AO' => 'AOA',
        'AQ' => 'AQD',
        'BA' => 'BAM',
        'CD' => 'CDF',
        'GH' => 'GHS',
        'GG' => 'GGP',
        'IM' => 'GBP',
        'LA' => 'LAK',
        'MO' => 'MOP',
        'ME' => 'EUR',
        'PS' => 'JOD',
        'BL' => 'EUR',
        'SH' => 'GBP',
        'MF' => 'ANG',
        'PM' => 'EUR',
        'RS' => 'RSD',
        'USAF' => 'USD',
    ];

    /**
     * Mapping for 249 countries taken from https://gist.github.com/marcusbaguley/304261
     *
     * @var array
     */
    const TERRITORY_TO_COUNTRY = [
        'NZ' => 'New Zealand',
        'CK' => 'Cook Islands',
        'NU' => 'Niue',
        'PN' => 'Pitcairn',
        'TK' => 'Tokelau',
        'AU' => 'Australian',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'HM' => 'Heard and Mc Donald Islands',
        'KI' => 'Kiribati',
        'NR' => 'Nauru',
        'NF' => 'Norfolk Island',
        'TV' => 'Tuvalu',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AT' => 'Austria',
        'BE' => 'Belgium',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'TF' => 'French Southern Territories',
        'DE' => 'Germany',
        'GR' => 'Greece',
        'GP' => 'Guadeloupe',
        'IE' => 'Ireland',
        'IT' => 'Italy',
        'LU' => 'Luxembourg',
        'MQ' => 'Martinique',
        'YT' => 'Mayotte',
        'MC' => 'Monaco',
        'NL' => 'Netherlands',
        'PT' => 'Portugal',
        'RE' => 'Reunion',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'SI' => 'Slovenia',
        'ES' => 'Spain',
        'VA' => 'Vatican City State (Holy See)',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'GB' => 'United Kingdom',
        'JE' => 'Jersey',
        'IO' => 'British Indian Ocean Territory',
        'GU' => 'Guam',
        'MH' => 'Marshall Islands',
        'FM' => 'Micronesia Federated States of',
        'MP' => 'Northern Mariana Islands',
        'PW' => 'Palau',
        'PR' => 'Puerto Rico',
        'TC' => 'Turks and Caicos Islands',
        'US' => 'United States',
        'UM' => 'United States Minor Outlying Islands',
        'VG' => 'Virgin Islands (British)',
        'VI' => 'Virgin Islands (US)',
        'HK' => 'Hong Kong',
        'CA' => 'Canada',
        'JP' => 'Japan',
        'AF' => 'Afghanistan',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AI' => 'Anguilla',
        'AG' => 'Antigua and Barbuda',
        'DM' => 'Dominica',
        'GD' => 'Grenada',
        'MS' => 'Montserrat',
        'KN' => 'Saint Kitts',
        'LC' => 'Saint Lucia',
        'VC' => 'Saint Vincent Grenadines',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AN' => 'Netherlands Antilles',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BF' => 'Burkina Faso',
        'GW' => 'Guinea-Bissau',
        'CI' => 'Ivory Coast',
        'ML' => 'Mali',
        'NE' => 'Niger',
        'SN' => 'Senegal',
        'TG' => 'Togo',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'IN' => 'India',
        'BO' => 'Bolivia',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'NO' => 'Norway',
        'SJ' => 'Svalbard and Jan Mayen Islands',
        'BR' => 'Brazil',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CG' => 'Congo Republic of the Democratic',
        'GQ' => 'Equatorial Guinea',
        'GA' => 'Gabon',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CL' => 'Chile',
        'CN' => 'China',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CD' => 'Congo-Brazzaville',
        'CR' => 'Costa Rica',
        'HR' => 'Croatia (Hrvatska)',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'FO' => 'Faroe Islands',
        'GL' => 'Greenland',
        'DJ' => 'Djibouti',
        'DO' => 'Dominican Republic',
        'TP' => 'East Timor',
        'ID' => 'Indonesia',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'ER' => 'Eritrea',
        'ET' => 'Ethiopia',
        'EE' => 'Estonia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FJ' => 'Fiji',
        'PF' => 'French Polynesia',
        'NC' => 'New Caledonia',
        'WF' => 'Wallis and Futuna Islands',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'GI' => 'Gibraltar',
        'GT' => 'Guatemala',
        'GN' => 'Guinea',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HN' => 'Honduras',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IR' => 'Iran (Islamic Republic of)',
        'IQ' => 'Iraq',
        'IL' => 'Israel',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KP' => 'Korea North',
        'KR' => 'Korea South',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao PeopleÕs Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libyan Arab Jamahiriya',
        'LI' => 'Liechtenstein',
        'CH' => 'Switzerland',
        'LT' => 'Lithuania',
        'MO' => 'Macau',
        'MK' => 'Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'MT' => 'Malta',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'MX' => 'Mexico',
        'MD' => 'Moldova Republic of',
        'MN' => 'Mongolia',
        'MA' => 'Morocco',
        'EH' => 'Western Sahara',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NP' => 'Nepal',
        'NI' => 'Nicaragua',
        'NG' => 'Nigeria',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PL' => 'Poland',
        'QA' => 'Qatar',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SK' => 'Slovakia (Slovak Republic)',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
        'AX' => 'Aland Islands',
        'AO' => 'Angola',
        'AQ' => 'Antarctica',
        'BA' => 'Bosnia and Herzegovina',
        'CD' => 'Congo (Kinshasa)',
        'GH' => 'Ghana',
        'GG' => 'Guernsey',
        'IM' => 'Isle of Man',
        'LA' => 'Laos',
        'MO' => 'Macao S.A.R.',
        'ME' => 'Montenegro',
        'PS' => 'Palestinian Territory',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'MF' => 'Saint Martin (French part)',
        'PM' => 'Saint Pierre and Miquelon',
        'RS' => 'Serbia',
        'USAF' => 'US Armed Forces',
    ];

    public static function createFromPosix(string $locale): Locale
    {
        if (0 === preg_match(self::LOCALE, $locale, $matches)) {
            throw new \InvalidArgumentException(
                "The given locale does not match <language[_territory[.codeset]][@modifier]>"
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
        return strtoupper($language);
    }

    /**
     * @param $modifier
     * @return string
     */
    private static function modifierToCurrency(string $modifier): string
    {
        switch ($modifier) {
            case 'EUR':
            case 'euro':
                return 'EUR';
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
