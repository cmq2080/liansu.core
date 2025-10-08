<?php

namespace liansu\facade;

use liansu\Facade;

/**
 * @method void initialize($configFiles, $tmpConfigs = null)
 * @method mixed get($key, $default = null)
 * @method void set($key, $value)
 * @method void remove($key)
 */
class Config extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Config';
    }
}
