<?php

/**
 * 描述：
 * Created at 2021/8/22 16:58 by mq
 */

namespace liansu\facade;

use liansu\Facade;

class Argument extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Argument';
    }
}
