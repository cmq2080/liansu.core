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

    /**
     * Summary of aspect
     * @param callable $func
     * @return void
     */
    protected function aspect(callable $func)
    {
        $tmpData = $this->data;
        $tmpData = array_serialize($tmpData);

        $tmpData = $func($tmpData);

        $tmpData = array_unserialize($tmpData);
        $this->data = $tmpData;
    }

    public function get($key, $default = null)
    {
        // 如果和set一样用切片的话，则空数组和不存在的值都会返回空数组，无法区分
        $keys = explode('.', $key);
        $tmpData = $this->data;
        foreach ($keys as $key2) {
            if (!isset($tmpData[$key2])) {
                return $default;
            }

            $tmpData = $tmpData[$key2];
        }

        return $tmpData;
    }

    public function set($key, $value)
    {
        // 先删除，再设置
        $this->remove($key);
        $this->aspect(function ($tmpData) use ($key, $value) {
            $tmpData[$key] = $value;

            return $tmpData;
        });
    }

    public function remove($key)
    {
        $this->aspect(function ($tmpData) use ($key) {
            // 删除其自身及子属性
            foreach ($tmpData as $k => $v) {
                if (strpos($k, $key) === 0) {
                    unset($tmpData[$k]);
                }
            }

            return $tmpData;
        });
    }
}
