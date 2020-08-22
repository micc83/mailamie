<?php

namespace Mailamie;

use Exception;

class Config
{
    private array $params;
    private ?array $altParams;

    public function __construct(array $params, array $altParams = null)
    {
        $this->params = $params;
        $this->altParams = $altParams;
    }

    /**
     * @param string $key
     * @return array|mixed|null
     * @throws Exception
     */
    public function get(string $key)
    {
        if ($this->altParams) {
            try {
                return static::dotGet($key, $this->altParams);
            } catch (Exception $e) {
            }
        }

        return static::dotGet($key, $this->params);
    }

    /**
     * @param string $key
     * @param array $data
     * @return array|mixed|null
     * @throws Exception
     */
    private static function dotGet(string $key, array $data)
    {
        $keys = explode('.', $key);

        foreach ($keys as $innerKey) {
            if (!array_key_exists($innerKey, $data)) {
                throw new Exception('Cannot find the given key in config.');
            }

            $data = $data[$innerKey];
        }

        return $data;
    }
}