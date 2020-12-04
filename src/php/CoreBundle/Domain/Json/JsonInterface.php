<?php

namespace Frontastic\Common\CoreBundle\Domain\Json;

interface JsonInterface
{
    /**
     * @param $data
     * @param int $flags
     * @param int $depth
     * @return string|false
     * @throws InvalidJsonEncodeException
     */
    public static function encode($data, int $flags, int $depth);

    /**
     * @param $data
     * @param $associative
     * @param int $depth
     * @param int $flags
     * @param $useNativeDecoder
     * @return mixed
     * @throws InvalidJsonDecodeException
     */
    public static function decode($data, $associative, int $depth, int $flags, $useNativeDecoder);
}
