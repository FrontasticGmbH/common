<?php

namespace Frontastic\Common\CoreBundle\Domain\Json;

class Json implements JsonInterface
{
    /**
     * @param $data
     * @param int $flags
     * @param int $depth
     * @return false|string
     * @throws InvalidJsonEncodeException
     */
    public static function encode($data, int $flags = 0, int $depth = 512)
    {
        $result = json_encode($data, $flags, $depth);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonEncodeException(json_last_error());
        }

        return $result;
    }

    /**
     * @param $data
     * @param false $associative
     * @param int $depth
     * @param int $flags
     * @param bool $useNativeDecoder
     * @return mixed
     * @throws InvalidJsonDecodeException
     */
    public static function decode(
        $data,
        $associative = false,
        int $depth = 512,
        int $flags = 0,
        $useNativeDecoder = false
    ) {
        if (!$useNativeDecoder && function_exists('simdjson_decode')) {
            try {
                return simdjson_decode($data, $associative, $depth);
            } catch (\Exception $e) {
                // TODO: modify Json::decode calls to handle exceptions
                // throw new InvalidJsonDecodeException($e->getMessage(), $e->getCode());

                // Ignore errors decoding data since body could be not Json
                return json_decode($data, $associative, $depth, $flags);
            }
        }
        return json_decode($data, $associative, $depth, $flags);
    }
}
