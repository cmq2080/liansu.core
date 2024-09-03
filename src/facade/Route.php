<?php

namespace liansu\facade;

use liansu\Facade;

class Route extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Route';
    }
}
