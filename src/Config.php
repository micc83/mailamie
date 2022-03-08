<?php declare(strict_types=1);

namespace Mailamie;

use Exception;

class Config
{
    /** @var array<string, string|array<string>|null> */
    private array $params;
    /** @var array<string, string|array<string>|null>|null */
    private ?array $altParams;

    const VERSION = "1.0.4";
    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * Config constructor.
     * @param array<string|array<string>|null> $params
     * @param array<string|array<string>|null>|null $altParams
     */
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
                return self::dotGet($key, $this->altParams);
            } catch (Exception $e) {
            }
        }

        return self::dotGet($key, $this->params);
    }

    /**
     * @param string $key
     * @param array<string, string|array<string>|null> $data
     * @return array<string, string|array<string>|null>|null|string
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
