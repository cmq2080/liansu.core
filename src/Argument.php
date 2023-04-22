<?php

/**
 * 描述：
 * Created at 2021/8/22 16:58 by mq
 */

namespace liansu\core;

class Argument
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

        if ($argv) {
            for ($i = 0; $i < count($argv); $i++) {
                $arg = $argv[$i];
                $key = $value = null;
                /*
                 * --api --app // 长参数
                 * --api 1
                 * -app 1 // 短参数
                 * --api=1 // 长联值参数
                 * -api=1 // 短联值参数
                 */
                if (strpos($arg, '--') === 0) { // 变长参数为短参数
                    $arg = substr($arg, 1);
                }

                if (self::isArgument($arg) === true) { // 找键
                    $key = substr($arg, 1);
                    $value = true; // 值默认为true

                    if (isset($argv[$i + 1])) {
                        $nextArg = $argv[$i + 1];
                        if (!self::isArgument($nextArg) && !self::isArgumentWithValue($nextArg)) { // 下一个参数绝对不作为参数名出现，那应该就是值了
                            $value = $nextArg;
                        }
                    }
                } else if (self::isArgumentWithValue($arg)) {
                    preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg, $matches);
                    $key = $matches[1];
                    $value = $matches[2];
                } else {
                    continue;
                }
                self::$data[$key] = $value;
            }
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
    // public static function set($key, $value)
    // {
    //     // TODO: Implement set() method.
    // }

    private static function isArgument($arg)
    {
        return boolval(preg_match('/^\-[0-9A-Za-z_-]+$/is', $arg));
    }

    private static function isArgumentWithValue($arg)
    {
        return boolval(preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg));
    }
}
