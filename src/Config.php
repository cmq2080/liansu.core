<?php

namespace liansu\core;

class Config
{
    private static $data = [];

    public static function initialize($configFiles, $tmpConfigs = null)
    {
        $data = [];
        foreach ($configFiles as $configFile) {
            $filename = pathinfo($configFile, PATHINFO_FILENAME);
            $data[$filename] = require $configFile;
        }

        if ($tmpConfigs) {
            $data = array_merge($data, $tmpConfigs);
        }

        self::$data = $data;
    }

    public static function get($key, $default = null)
    {
        $tmpData = self::$data;
        $tmpData = Helper::array_serialize($tmpData);

        return $tmpData[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        $tmpData = self::$data;
        $tmpData = Helper::array_serialize($tmpData);

        $tmpData[$key] = $value;

        $tmpData = Helper::array_unserialize($tmpData);
        self::$data = $tmpData;
    }

    public static function remove($key)
    {
        $tmpData = self::$data;
        $tmpData = Helper::array_serialize($tmpData);

        unset($tmpData[$key]);

        $tmpData = Helper::array_unserialize($tmpData);
        self::$data = $tmpData;
    }
}
