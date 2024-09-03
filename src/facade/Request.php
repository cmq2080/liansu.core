<?php

/**
 * 描述：
 * Created at 2021/5/30 21:44 by mq
 */

namespace liansu\facade;

use liansu\Facade;

class Request extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Request';
    }
}
