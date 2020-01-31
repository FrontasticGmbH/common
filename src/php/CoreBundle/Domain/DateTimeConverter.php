<?php

namespace Frontastic\Common\CoreBundle\Domain;

class DateTimeConverter
{
    public static function dateTimeInterfaceToImmutable(\DateTimeInterface $original): \DateTimeImmutable
    {
        if ($original instanceof \DateTimeImmutable) {
            return $original;
        }

        if ($original instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($original);
        }

        return \DateTimeImmutable::createFromFormat(
            \DateTime::ISO8601,
            $original->format(\DateTime::ISO8601),
            $original->getTimezone()
        );
    }

    public static function dateTimeInterfaceToMutable(\DateTimeInterface $original): \DateTime
    {
        if ($original instanceof \DateTime) {
            return $original;
        }

        if ($original instanceof \DateTimeImmutable) {
            return \DateTime::createFromImmutable($original);
        }

        return \DateTime::createFromFormat(
            \DateTime::ISO8601,
            $original->format(\DateTime::ISO8601),
            $original->getTimezone()
        );
    }
}
