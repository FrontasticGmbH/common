<?php

namespace Frontastic\Common\SprykerBundle\Domain;

interface SprykerClientInterface
{
    public const MODE_SYNC = 'sync';
    public const MODE_ASYNC = 'async';

    public const METHOD_GET = 'GET';
    public const METHOD_HEAD = 'HEAD';
    public const METHOD_POST = 'POST';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $mode
     * @return mixed
     */
    public function get(string $endpoint, array $headers = [], string $mode = self::MODE_SYNC);

    /**
     * @param string $endpoint
     * @param array $headers
     * @return mixed
     */
    public function head(string $endpoint, array $headers = []);

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     * @param string $mode
     * @return mixed
     */
    public function post(
        string $endpoint,
        array $headers = [],
        string $body = '',
        string $mode = self::MODE_SYNC
    );

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     * @return mixed
     */
    public function patch(string $endpoint, array $headers = [], string $body = '');

    /**
     * @param string $endpoint
     * @param array $headers
     * @return mixed
     */
    public function delete(string $endpoint, array $headers = []);
}
