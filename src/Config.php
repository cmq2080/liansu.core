<?php

namespace liansu;

use liansu\facade\Helper;

class Config
{
    private $data = [];

    public function initialize($configFiles, $tmpConfigs = null)
    {
        $data = [];
        foreach ($configFiles as $configFile) {
            $filename = pathinfo($configFile, PATHINFO_FILENAME);
            $data[$filename] = require $configFile;
        }

        if ($tmpConfigs) {
            $data = array_merge($data, $tmpConfigs);
        }

        $this->data = $data;
    }

    public function get($key, $default = null)
    {
        $tmpData = $this->data;
        $tmpData = Helper::array_serialize($tmpData);

        return $tmpData[$key] ?? $default;
    }

    protected function modify(callable $func)
    {
        $tmpData = $this->data;
        $tmpData = Helper::array_serialize($tmpData);

        $tmpData = $func($tmpData);

        $tmpData = Helper::array_unserialize($tmpData);
        $this->data = $tmpData;
    }

    public function set($key, $value)
    {
        $this->modify(function ($tmpData) use ($key, $value) {
            $tmpData[$key] = $value;

            return $tmpData;
        });
    }

    public function remove($key)
    {
        $this->modify(function ($tmpData) use ($key) {
            unset($tmpData[$key]);
        });
    }
}
