<?php

namespace liansu\facade;

use liansu\Facade;

class Config extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Config';
    }
}
