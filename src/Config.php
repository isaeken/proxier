<?php

namespace IsaEken\Proxier;

/**
 * Class Config
 * Its only use for this package is to get config values.
 * For Laravel, you can use config('proxier.key').
 *
 * @package IsaEken\Proxier
 */
class Config
{
    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public array $config = [];

    public function __construct()
    {
        $this->config = require __DIR__ . "/../config/proxier.php";
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
