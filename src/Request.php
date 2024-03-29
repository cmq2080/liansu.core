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
    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    private static function initArguments()
    {
        Argument::initialize();
        self::$args = Argument::get();
    }

    public static function getMethod()
    {
        if (self::isCli()) {
            return null;
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isMethod($method): bool
    {
        if (!self::isCli()) {
            return self::getMethod() == strtoupper($method);
        }

        return false;
    }

    public static function isGet(): bool
    {
        return self::isMethod('GET');
    }

    public static function isPost(): bool
    {
        return self::isMethod('POST');
    }

    public static function isAjax(): bool
    {
        if (!self::isCli()) {
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
                return true;
            }
        }

        return false;
    }
}
