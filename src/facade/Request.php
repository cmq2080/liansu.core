<?php

/**
 * 描述：
 * Created at 2021/5/30 21:44 by mq
 */

namespace liansu\facade;

use liansu\Facade;

/**
 * @method void initialize()
 * @method mixed get($key = null, $default = null)
 * @method mixed post($key = null, $default = null)
 * @method mixed args($key = null, $default = null)
 * @method mixed all($key = null, $default = null)
 * @method bool isGet()
 * @method bool isPost()
 * @method bool isCli()
 * @method string|bool getMethod()
 * @method bool isMethod($method)
 * @method bool isAjax()
 */
class Request extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Request';
    }
}
