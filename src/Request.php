<?php

/**
 * 描述：
 * Created at 2021/5/30 21:44 by mq
 */

namespace liansu\core;


class Request
{
    private static $args = [];

    /**
     * 功能：初始化
     * Created at 2021/8/22 16:12 by mq
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        if (self::isCli() === true) {
            self::initArguments();
        }
    }

    private static function _get($method, $key = null, $default = null)
    {
        if ($method === 'GET') {
            return $key === null ? $_GET : ($_GET[$key] ?? $default);
        } else if ($method === 'POST') {
            return $key === null ? $_POST : ($_POST[$key] ?? $default);
        } else if ($method === 'ARGS') {
            return $key === null ? self::$args : (self::$args[$key] ?? $default);
        } else if ($method === 'ALL') {
            $allData = array_merge($_REQUEST, self::$args);
            return $key === null ? $allData : ($allData[$key] ?? $default);
        }

        return $default;
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        if (in_array(strtoupper($name), ['GET', 'POST', 'ARGS', 'ALL']) === true) {
            return self::_get(strtoupper($name), $arguments[0] ?? null, $arguments[1] ?? null);
        }

        return null;
    }

    /**
     * 功能：
     * Created at 2021/8/22 21:35 by mq
     * @return bool
     */
    public static function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    private static function initArguments()
    {
        Argument::initialize();
        self::$args = Argument::get();
    }
}
