<?php
/**
 * 描述：
 * Created at 2021/6/6 22:48 by mq
 */

namespace liansu\core;


use liansu\core\interface_set\InitInterface;

class Route implements InitInterface
{
    private static $data = [];
    private static $runnerDesc = 'controller';
    private static $actionDesc = 'action';
    private static $defaultAction = 'index';

    public static function initialize()
    {
        if (defined('CONFIG_DIRECTORY') === true && is_file(CONFIG_DIRECTORY . '/route.php') === true) {
            self::$data = require(CONFIG_DIRECTORY . '/route.php');
        }

        if (Request::isCli() === true) {
            self::$runnerDesc = 'runner';
            self::$defaultAction = 'run';
        }

        foreach (self::$data as $key => $value) {
            self::$data[$key] = self::formatController($value);
        }
    }

    public static function setDefaultAction($defaultAction)
    {
        self::$defaultAction = $defaultAction;
    }

    public static function execute($controller, $action)
    {
        if (class_exists($controller) === false) {
            throw new \Exception(self::$runnerDesc . '不存在：' . $controller);
        }
//        if (($controller instanceof Controller) === false) {
//            throw new \Exception('controller必须是liansu\类的子类：' . $controller);
//        }

        $class = new \ReflectionClass($controller);
        if ($class->hasMethod($action) === false) { // 通过反射类来查找action的有无，我当时有点猛~~~
            throw new \Exception(self::$actionDesc . '不存在：' . $action);
        }
        $method = $class->getMethod($action);
        if ($method->isStatic() === true) { // 然后再看该方法是否是动态
            throw new \Exception(self::$actionDesc . '不能是静态的');
        }
        if ($method->isPublic() === false) { // 然后再看该方法是否是公有的
            throw new \Exception(self::$actionDesc . '必须是公有的');
        }

        App::instance()->setRunner($controller)->setAction($action); // 向App类中注入临时controller及action
        (new $controller())->$action();
    }

    public static function find($app)
    {
        $tmp = self::formatController($app);
        if (isset(self::$data[$tmp]) === true) {
            $app = self::$data[$tmp];
        }

        return $app;
    }

    public static function formatController($value)
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