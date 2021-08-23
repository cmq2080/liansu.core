<?php
/**
 * 描述：
 * Created at 2021/8/22 16:58 by mq
 */

namespace liansu\core;


use liansu\core\interface_set\InitInterface;

class Argument implements InitInterface
{
    private static $data = [];

    /**
     * 功能：初始化
     * Created at 2021/8/22 16:12 by mq
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        global $argv;

        for ($i = 0; $i < count($argv); $i++) {
            $arg = $argv[$i];
            $key = $value = null;
//            if ($i === 0) {
            /*
             * --api --app
             * --api 1
             * -app 1
             * --api=1
             * -api=1
             *
             */
//            }
            if (strpos($arg, '--') === 0) { // 变长参数为短参数
                $arg = substr($arg, 1);
            }

            if (self::isArgument($arg) === true) {
                if (self::isLongArgument($arg) === true) {
                    $key = substr($arg, 2);
                } else if (self::isShortArgument($arg) === true) {
                    $key = substr($arg, 1);
                }

                if (isset($argv[$i + 1]) === false) {
                    $value = true;
                } else if (self::isArgument($argv[$i + 1]) === true) {
                    $value = true;
                } else {
                    $value = $argv[$i + 1];
                    $i++;
                }
            } else if (self::isArgumentWithValue($arg)) {
                preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg, $matches);
                $key = $matches[1];
                $value = $matches[2];
            } else {
                continue;
            }
            self::$data[$key] = $value;

            continue;
        }
    }

    /**
     * 功能：获取参数
     * Created at 2021/8/22 16:13 by mq
     * @param null $key
     * @param string $default
     * @return mixed
     */
    public static function get($key = null, $default = '')
    {
        // TODO: Implement get() method.
        return $key === null ? self::$data : (self::$data[$key] ?? $default);
    }

    /**
     * 功能：设置参数
     * Created at 2021/8/22 16:14 by mq
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    private static function isArgument($arg)
    {
        return self::isShortArgument($arg);
    }

    /**
     * 功能：
     * Created at 2021/8/22 16:35 by mq
     * @param $arg
     * @return bool
     */
    private static function isLongArgument($arg)
    {
        return boolval(preg_match('/^\-\-[0-9A-Za-z_-]+$/is', $arg));
    }

    /**
     * 功能：
     * Created at 2021/8/22 16:35 by mq
     * @param $arg
     * @return bool
     */
    private static function isShortArgument($arg)
    {
        return boolval(preg_match('/^\-[0-9A-Za-z_-]+$/is', $arg));
    }

    private static function isArgumentWithValue($arg)
    {
        return self::isShortArgumentWithValue($arg);
    }

    /**
     * 功能：
     * Created at 2021/8/22 16:35 by mq
     * @param $arg
     * @return bool
     */
    private static function isLongArgumentWithValue($arg)
    {
        return boolval(preg_match('/^\-\-([0-9A-Za-z_-]+)=([0-9A-Za-z_-]+)$/is', $arg));
    }

    /**
     * 功能：
     * Created at 2021/8/22 16:35 by mq
     * @param $arg
     * @return bool
     */
    private static function isShortArgumentWithValue($arg)
    {
        return boolval(preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg));
    }
}