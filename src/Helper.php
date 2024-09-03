<?php

namespace liansu;

use ReflectionClass;

class Helper
{
    public function str_replace($needle, $replace, $object)
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

    // public function url($runner, $action = null)
    // {
    //     $paramName = App::instance()->getRouteParamName();
    //     if (!$action) {
    //         $action = App::instance()->getDefaultApp();
    //     }
    //     $url = '?' . $paramName . '=' . $runner . '@' . $action;
    //     return $url;
    // }

    public function is_json($str)
    {
        $result = json_decode($str);
        return $result === null ? false : true;
    }

    /**
     * @param array|callable|string $needle
     * @return array|bool
     */
    public function get_as_array($needle)
    {
        $result = false;
        if (is_array($needle)) {
            $result = $needle;
        } else if (is_callable($needle)) {
            $result = $needle();
        } else if (is_string($needle)) {
            if ($this->is_json($needle)) {
                $result = json_decode($needle, true);
            } else if (is_file($needle)) {
                $result = require $needle;
            }
        }

        return is_array($result) ? $result : false;
    }

    /**
     * 数组递级序列化
     */
    public function array_serialize($haystack, $position = '')
    {
        $res = [];
        foreach ($haystack as $key => $value) {
            // 分析键
            $type = '';
            $newKey = ltrim($position . '.' . $type . $key, '.');

            // 分析值
            if (is_array($value)) {
                $res = array_merge($res, $this->array_serialize($value, $newKey));
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
    public function array_unserialize($serializedArr)
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

    public function getRootDirectory()
    {
        if (defined('ROOT_DIRECTORY')) {
            return ROOT_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../..');
    }

    public function getPublicDirectory()
    {
        if (defined('PUBLIC_DIRECTORY')) {
            return PUBLIC_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../public');
    }

    public function getRuntimeDirectory()
    {
        if (defined('RUNTIME_DIRECTORY')) {
            return RUNTIME_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../runtime');
    }

    public function getVendorDirectory()
    {
        if (defined('VENDOR_DIRECTORY')) {
            return VENDOR_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../vendor');
    }

    public function initDirectory($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function clearDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $node) {
            if ($node == '.' || $node == '..') {
                continue;
            }

            $filepath = $dir . '/' . $node;
            if (is_file($filepath)) {
                unlink($filepath);
            } else if (is_dir($filepath)) {
                $this->clearDirectory($filepath);
                rmdir($filepath);
            }
        }
    }

    public function scanActions($runner)
    {
        $reflectionClass = new ReflectionClass($runner);
        $actions = [];
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            if (!$method->isPublic()) {
                continue;
            }
            if ($method->isStatic()) {
                continue;
            }
            if ($method->getName() == '__construct') {
                continue;
            }

            $actions = $method->getName();
        }

        return $actions;
    }

    public function module_exists($module)
    {
        $vendorDir = $this->getVendorDirectory();
        $filepath = $vendorDir . '/' . $module . '/composer.json';

        return is_file($filepath);
    }
}
