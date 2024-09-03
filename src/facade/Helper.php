<?php

namespace liansu\facade;

use liansu\Facade;

class Helper extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Helper';
    }
}
