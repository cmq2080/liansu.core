<?php

namespace liansu\facade;

use liansu\Facade;

/**
 * @method void initialize()
 * @method array parse()
 * @method string getRouteParam()
 * @method string getRouteDelimiter()
 */
class Route extends Facade
{
    protected static function getApplicationClassName(): string
    {
        return '\\liansu\\Route';
    }
}
