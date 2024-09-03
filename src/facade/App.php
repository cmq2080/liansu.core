<?php

namespace liansu\facade;

class App
{
    protected static function getApplicationClassName(): string
    {
        return 'app';
    }

    protected static function instance()
    {
        $applicationClassName = static::getApplicationClassName();

        $instance = App::instance()->getBindingApp($applicationClassName);

        return $instance;
    }
}
