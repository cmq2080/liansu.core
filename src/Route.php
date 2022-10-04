<?php

/**
 * 描述：
 * Created at 2021/6/6 22:48 by mq
 */

namespace liansu\core;

class Route
{
    private static $data = [];
    private static $runnerDesc = 'Controller';
    private static $actionDesc = 'Action';
    private static $defaultAction = 'index';

    public static function initialize()
    {
        if (is_file(App::instance()->configDirectory . '/route.php')) {
            self::$data = require App::instance()->configDirectory . '/route.php';
        }

        if (Request::isCli() === true) {
            self::$runnerDesc = 'Runner';
            self::$defaultAction = 'Run';
        }

        foreach (self::$data as $key => $value) {
            self::$data[$key] = self::formatRunner($value);
        }
    }

    public static function setDefaultAction($defaultAction)
    {
        self::$defaultAction = $defaultAction;
    }

    public static function execute($runner, $action)
    {
        if (!class_exists($runner)) {
            throw new \Exception(self::$runnerDesc . '不存在：' . $runner);
        }

        $class = new \ReflectionClass($runner);
        if (!$class->hasMethod($action)) { // 通过反射类来查找action的有无，我当时有点猛~~~
            throw new \Exception(self::$actionDesc . '不存在：' . $action);
        }
        $method = $class->getMethod($action); // 这个方法必须是公有动态的
        if (!$method->isPublic()) { // 然后再看该方法是否是公有的
            throw new \Exception(self::$actionDesc . '必须是公有的');
        }
        if ($method->isStatic()) { // 然后再看该方法是否是动态
            throw new \Exception(self::$actionDesc . '不能是静态的');
        }

        App::instance()->setRunner($runner)->setAction($action); // 向App类中注入临时runner及action
        (new $runner())->$action();
    }

    public static function find($app)
    {
        $tmp = self::formatRunner($app);
        if (isset(self::$data[$tmp]) === true) {
            $app = self::$data[$tmp];
        }

        return $app;
    }

    public static function formatRunner($value)
    {
        return ltrim(Helper::str_replace(['/', '.'], '\\', $value), '\\');
    }

    public static function parseStr($app, $target = 'controller')
    {
        $result = null;
        if ($target === 'controller') {
            $result = str_replace('/', '\\', explode('@', $app)[0]);
        } else if ($target === 'action') {
            $result = explode('@', $app)[1] ?? self::$defaultAction;
        }

        return $result;
    }
}
