<?php
if (!function_exists('str_replace_all')) {
    function str_replace_all($needle, $replace, $object)
    {
        if (is_array($needle) === true) { // 多个搜索项容易形成积压替换（前一个的替换结果刚好构成第二个的替换要件）
            foreach ($needle as $value) {
                $object = str_replace($value, $replace, $object);
            }
        } else {
            $object = str_replace($needle, $replace, $object);
        }

        return $object;
    }
}

if (!function_exists('is_json')) {
    function is_json($str)
    {
        $result = json_decode($str);
        return $result === null ? false : true;
    }
}

if (!function_exists('get_as_array')) {
    /**
     * @param array|callable|string $needle
     * @return array|bool
     */
    function get_as_array($needle)
    {
        $result = false;
        if (is_array($needle)) {
            $result = $needle;
        } else if (is_callable($needle)) {
            $result = $needle();
        } else if (is_string($needle)) {
            if (is_json($needle)) {
                $result = json_decode($needle, true);
            } else if (is_file($needle)) {
                $result = require $needle;
            }
        }

        return is_array($result) ? $result : false;
    }
}

if (!function_exists('array_serialize')) {
    /**
     * 数组递级序列化
     */
    function array_serialize($haystack, $position = '')
    {
        $res = [];
        foreach ($haystack as $key => $value) {
            // 分析键
            $type = '';
            $newKey = ltrim($position . '.' . $type . $key, '.');

            // 分析值
            if (is_array($value)) {
                $res = array_merge($res, array_serialize($value, $newKey));
            } else {

                $newValue = $value;
                $res[$newKey] = $newValue;
            }
        }

        return $res;
    }
}

if (!function_exists('array_unserialize')) {
    /**
     * 数组递级反序列化
     */
    function array_unserialize($serializedArr)
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
}

if (!function_exists('get_root_dir')) {
    function get_root_dir()
    {
        if (defined('ROOT_DIRECTORY')) {
            return ROOT_DIRECTORY;
        }

        return realpath(__DIR__ . '/../../../../..');
    }
}

if (!function_exists('get_public_dir')) {
    function get_public_dir()
    {
        if (defined('PUBLIC_DIRECTORY')) {
            return PUBLIC_DIRECTORY;
        }

        return get_root_dir() . '/public';
    }
}

if (!function_exists('get_runtime_dir')) {
    function get_runtime_dir()
    {
        if (defined('RUNTIME_DIRECTORY')) {
            return RUNTIME_DIRECTORY;
        }

        return get_root_dir() . '/runtime';
    }
}

if (!function_exists('get_vendor_dir')) {
    function get_vendor_dir()
    {
        if (defined('VENDOR_DIRECTORY')) {
            return VENDOR_DIRECTORY;
        }

        return get_root_dir() . '/vendor';
    }
}

if (!function_exists('init_dir')) {
    function init_dir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

if (!function_exists('clear_dir')) {
    function clear_dir($dir)
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
                clear_dir($filepath);
                rmdir($filepath);
            }
        }
    }
}

if (!function_exists('scan_for_actions')) {
    function scan_for_actions($runner)
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
}

if (!function_exists('module_exists')) {
    function module_exists($module)
    {
        $vendorDir = get_vendor_dir();
        $filepath = $vendorDir . '/' . $module . '/composer.json';

        return is_file($filepath);
    }
}











