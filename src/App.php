<?php

namespace liansu\core;

use liansu\core\traits\TAppConfig;
use liansu\core\traits\TAppDefault;
use liansu\core\traits\TAppInit;
use ReflectionClass;

class App
{
    use TAppDefault;
    use TAppConfig;
    use TAppInit;

    private static $instance = null;

    protected $baseNamespace = 'app';
    protected $namespaces = [];

    protected $runner;
    protected $action;

    public static function instance($configFiles = null)
    {
        if (!self::$instance) {
            // 这里使用延迟静态绑定
            self::$instance = new static();

            if ($configFiles) {
                self::$instance->setConfigFiles($configFiles);
            } else {
                foreach (scandir(CONFIG_DIRECTORY) as $node) {
                    $filepath = CONFIG_DIRECTORY . '/' . $node;
                    if (!is_file($filepath)) {
                        continue;
                    }

                    $configFiles[] = $filepath;
                }

                if ($configFiles) {
                    self::$instance->setConfigFiles($configFiles);
                }
            }
        }

        return self::$instance;
    }

    private function __construct()
    {
        defined('ROOT_DIRECTORY') || define('ROOT_DIRECTORY', realpath(__DIR__ . '/../../../..'));
        defined('PUBLIC_DIRECTORY') || define('PUBLIC_DIRECTORY', ROOT_DIRECTORY . '/public');
        defined('CONFIG_DIRECTORY') || define('CONFIG_DIRECTORY', ROOT_DIRECTORY . '/config');
        defined('RUNTIME_DIRECTORY') || define('RUNTIME_DIRECTORY', ROOT_DIRECTORY . '/runtime');
        defined('VENDOR_DIRECTORY') || define('VENDOR_DIRECTORY', ROOT_DIRECTORY . '/vendor');
    }

    public function setBaseNamespace($baseNamespace)
    {
        $this->baseNamespace = $baseNamespace;

        return $this;
    }

    public function getRunner()
    {
        return $this->runner;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function run()
    {
        try {
            // 运行初始化组件
            $this->runInits();

            array_unshift($this->namespaces, $this->baseNamespace);

            // 找寻路由
            $routeRes = Route::parse();
            $this->runner = $routeRes['runner'];
            $this->action = $routeRes['action'];

            // 找寻控制类
            $find = 0;
            foreach ($this->namespaces as $namespace) {
                $runner = $namespace . '\\' . $this->runner;
                if (!class_exists($runner)) {
                    continue;
                }

                $reflectionClass = new ReflectionClass($runner);
                if (!$reflectionClass->hasMethod($this->action)) {
                    continue;
                }
                $reflectionMethod = $reflectionClass->getMethod($this->action);
                if (!$reflectionMethod->isPublic()) {
                    continue;
                }
                if ($reflectionMethod->isStatic()) {
                    continue;
                }

                $this->runner = $runner;
                $find = 1;
                break;
            }

            if (!$find) {
                throw new \Exception('runner not found');
            }


            $driver = new $this->runner();
            $driver->{$this->action}();
        } catch (\Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
