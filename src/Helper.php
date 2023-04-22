<?php

namespace liansu\core;

class Helper
{
    public static function str_replace($needle, $replace, $object)
    {
        if (is_array($needle) === true) {
            foreach ($needle as $value) {
                $object = str_replace($value, $replace, $object);
            }
        } else {
            $object = str_replace($needle, $replace, $object);
        }

        return $object;
    }

    // public static function url($runner, $action = null)
    // {
    //     $paramName = App::instance()->getRouteParamName();
    //     if (!$action) {
    //         $action = App::instance()->getDefaultApp();
    //     }
    //     $url = '?' . $paramName . '=' . $runner . '@' . $action;
    //     return $url;
    // }

    /**
     * 数组递级序列化
     */
    public static function array_serialize($haystack, $position = '')
    {
        $res = [];
        foreach ($haystack as $key => $value) {
            // 分析键
            $type = '';
            $newKey = ltrim($position . '.' . $type . $key, '.');

            // 分析值
            if (is_array($value)) {
                $res = array_merge($res, self::array_serialize($value, $newKey));
            } else {

                $newValue = $value;
                $res[$newKey] = $newValue;
            }
        }

        return $res;
    }

    /**
     * 数组递级反序列化
     */
    public static function array_unserialize($serializedArr)
    {
        $res = [];
        foreach ($serializedArr as $key => $value) {
            $keys = explode('.', $key);
            $youbiao = &$res; // 写个游标吧
            foreach ($keys as $i => $newKey) {
                $newKey .= '';
                $isLast = $i == count($keys) - 1 ? true : false;

                if (!$isLast) {
                    if (!isset($youbiao[$newKey])) {
                        $youbiao[$newKey] = [];
                    }

                    $youbiao = &$youbiao[$newKey];
                    continue;
                }

                $youbiao[$newKey] = $value;
            }
        }

        return $res;
    }

    public static function getRootDirectory()
    {
        if (defined('ROOT_DIRECTORY')) {
            return ROOT_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../..');
    }

    public static function getPublicDirectory()
    {
        if (defined('PUBLIC_DIRECTORY')) {
            return PUBLIC_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../public');
    }

    public static function getRuntimeDirectory()
    {
        if (defined('RUNTIME_DIRECTORY')) {
            return RUNTIME_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../runtime');
    }
}
