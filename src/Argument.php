<?php

/**
 * 描述：
 * Created at 2021/8/22 16:58 by mq
 */

namespace liansu;

class Argument
{
    private $data = [];

    /**
     * 功能：初始化
     * Created at 2021/8/22 16:12 by mq
     */
    public function initialize()
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
                if (strpos($arg, '--') === 0) { // 变长（联值）参数为短（联值）参数
                    $arg = substr($arg, 1);
                }

                if ($this->isArgument($arg) === true) { // 找键
                    $key = substr($arg, 1);
                    $value = true; // 值默认为true

                    if (isset($argv[$i + 1])) {
                        $nextArg = $argv[$i + 1];
                        if (!$this->isArgument($nextArg) && !$this->isArgumentWithValue($nextArg)) { // 下一个参数绝对不作为参数名出现，那应该就是值了
                            $value = $nextArg;
                            $i++;
                        }
                    }
                } else if ($this->isArgumentWithValue($arg)) {
                    preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg, $matches);
                    $key = $matches[1];
                    $value = $matches[2];
                } else {
                    continue;
                }
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * 功能：获取参数
     * Created at 2021/8/22 16:13 by mq
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        // TODO: Implement get() method.
        return empty($key) ? $this->data : ($this->data[$key] ?? $default);
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

    /**
     * 判断是否是参数
     * @param string $arg
     * @return bool
     */
    private function isArgument($arg)
    {
        return boolval(preg_match('/^\-[0-9A-Za-z_-]+$/is', $arg));
    }

    /**
     * 判断是否是联值参数
     * @param string $arg
     * @return bool
     */
    private function isArgumentWithValue($arg)
    {
        return boolval(preg_match('/^\-([0-9A-Za-z_-]+)=(\S+)$/is', $arg));
    }
}
