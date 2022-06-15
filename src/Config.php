<?php

/**
 * 描述：
 * Created at 2021/5/30 21:42 by mq
 */

namespace liansu\core;


class Config
{
    private static $configFiles = [];
    private static $data = [];

    // TODO: Implement initialize() method.
    public static function initialize()
    {
        //        // 先加载主配置（app.php）文件
        //        if (is_file(CONFIG_DIRECTORY . '/app.php') === true) {
        //            self::$data = require_once CONFIG_DIRECTORY . '/app.php';
        //        }
        //
        //        // 再依次加载辅配置文件（辅配置级别低一级）
        //        foreach (scandir(CONFIG_DIRECTORY) as $file) {
        //            if ($file === '.' || $file === '..' || $file === 'app.php' || is_file(CONFIG_DIRECTORY . '/' . $file) === false) { // 不是文件，算了
        //                continue;
        //            }
        //            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') { // 还必须得是php文件
        //                continue;
        //            }
        //
        //            $key = pathinfo($file, PATHINFO_FILENAME);
        //            self::$data[$key] = require_once CONFIG_DIRECTORY . '/' . $file; // 有与主配置冲突的地方，直接覆盖
        //
        //            //            // app.php文件作主配置
        //            //            if (pathinfo($file, PATHINFO_BASENAME) === 'app.php') {
        //            //                self::$vars = require_once CONFIG_DIRECTORY . '/' . $file;
        //            //            } else { // 其它文件做辅配置，级别低一级
        //            //                $key = pathinfo($file, PATHINFO_FILENAME);
        //            //                self::$vars[$key] = require_once CONFIG_DIRECTORY . '/' . $file;
        //            //            }
        //        }
        foreach (self::$configFiles as $configFile) {
            $arr = require $configFile;
            if (is_array($arr) === false) {
                continue;
            }
            $filename = pathinfo($configFile, PATHINFO_FILENAME);
            self::$data[$filename] = self::$data[$filename] ?? [];
            self::$data[$filename] = array_merge(self::$data[$filename], $arr);
        }
    }

    public static function setConfigFiles($configFiles)
    {
        self::$configFiles = $configFiles;
        self::initialize();
    }

    /**
     * 功能：获取配置
     * Created at 2020/7/25 18:29 by mq
     * @param $key
     * @param string $default
     * @return array|mixed|null
     */
    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return self::$data;
        }
        // 以.为分界，数组分级获取
        $keys = explode('.', $key);
        $data = self::$data;
        foreach ($keys as $s_key) {
            if (isset($data[$s_key]) === false) {
                return $default;
            }

            $data = $data[$s_key];
        }

        return $data;
    }

    /**
     * 功能：设置配置
     * Created at 2021/8/22 21:53 by mq
     * @param $key
     * @param null $value
     */
    public static function set($key, $value = null)
    {
        if (is_array($key) === true) {
            foreach ($key as $k => $v) {
                self::$data[$k] = $v;
            }
        } else {
            self::$data[$key] = $value;
        }
    }

    /**
     * 功能：删除配置
     * Created at 2020/7/25 18:29 by mq
     * @param $key
     * @param $value
     */
    public static function remove($key)
    {
        unset(self::$data[$key]);
    }
}
