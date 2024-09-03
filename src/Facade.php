<?php

namespace liansu;

abstract class Facade
{
    abstract protected static function getApplicationClassName(): string;

    protected static function instance()
    {
        $applicationClassName = static::getApplicationClassName();

        $instance = App::instance()->getContainerInstance($applicationClassName);

        return $instance;
    }

    public static function __callStatic($name, $args)
    {
        // var_dump($name);
        // var_dump($args);
        return call_user_func_array([static::instance(), $name], $args);
    }
}
