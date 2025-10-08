<?php

namespace liansu;

use liansu\facade\Response;
use liansu\facade\Route;
use liansu\interfaces\IRun;
use liansu\traits\TAppConfig;
use liansu\traits\TAppContainer;
use liansu\traits\TAppDefault;
use liansu\traits\TAppInit;
use ReflectionClass;

class App
{
    use TAppDefault;
    use TAppContainer;
    use TAppConfig;
    use TAppInit;

    private static $instance = null;

    protected $firstNamespace = 'app';
    protected $namespaces = [];

    protected $namespace;
    protected $runner;
    protected $action;

    public static function instance($configFiles = null)
    {
        if (!self::$instance) {
            // 这里使用延迟静态绑定
            $instance = new static();
            // 初始化容器
            $instance->initializeContainer();

            if ($configFiles) {
                $instance->setConfigFiles($configFiles);
            } else {
                foreach (scandir(CONFIG_DIRECTORY) as $node) {
                    $filepath = CONFIG_DIRECTORY . '/' . $node;
                    if (!is_file($filepath)) {
                        continue;
                    }

                    $configFiles[] = $filepath;
                }

                if ($configFiles) {
                    $instance->setConfigFiles($configFiles);
                }
            }

            self::$instance = $instance;
        }

        return self::$instance;
    }

    protected function __construct()
    {
        defined('ROOT_DIRECTORY') || define('ROOT_DIRECTORY', realpath(__DIR__ . '/../../../..'));
        defined('PUBLIC_DIRECTORY') || define('PUBLIC_DIRECTORY', ROOT_DIRECTORY . '/public');
        defined('CONFIG_DIRECTORY') || define('CONFIG_DIRECTORY', ROOT_DIRECTORY . '/config');
        defined('RUNTIME_DIRECTORY') || define('RUNTIME_DIRECTORY', ROOT_DIRECTORY . '/runtime');
        defined('VENDOR_DIRECTORY') || define('VENDOR_DIRECTORY', ROOT_DIRECTORY . '/vendor');

        require __DIR__ . '/config/functions.php';
    }

    public function setFirstNamespace($firstNamespace)
    {
        if (is_string($$firstNamespace)) {
            $this->firstNamespace = $firstNamespace;
        }

        return $this;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Summary of getRunner
     * @return string
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Summary of getAction
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    public function run()
    {
        try {
            $this->preRun();

            // 运行初始化组件
            $this->runInits();

            // 找寻路由
            $routeRes = Route::parse();
            $runner = $routeRes['runner'];
            $action = $routeRes['action'];

            // 找寻控制类
            $find = false;
            foreach ($this->namespaces as $namespace) {
                $testRunner = $namespace . '\\' . $runner;
                if (!class_exists($testRunner)) {
                    continue;
                }
                $testAction = $action;

                $reflectionClass = new ReflectionClass($testRunner);
                if (!$reflectionClass->hasMethod($testAction)) { // 没有目标方法，不行
                    continue;
                }
                $reflectionMethod = $reflectionClass->getMethod($testAction);
                if ($reflectionMethod->isConstructor()) { // 目标方法是构造函数，不行
                    continue;
                }
                if (!$reflectionMethod->isPublic()) { // 目标方法是非公有的，不行
                    continue;
                }
                if ($reflectionMethod->isStatic()) { // 目标方法是静态的，不行
                    continue;
                }

                // 找到了
                $this->namespace = $namespace;
                $this->runner = $testRunner;
                $this->action = $testAction;
                $find = true;
                break;
            }

            if (!$find) {
                throw new \Exception('Runner Not Found');
            }

            $driver = new $this->runner(); // 其实是new {$this->runner}();
            $driver->{$this->action}();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    protected function preRun()
    {
        $this->autoloadInits();
        array_unshift($this->namespaces, $this->firstNamespace);
    }

    protected function autoloadInits()
    {
        $groupDir = VENDOR2_DIRECTORY . '/liansu';
        $initMapping = [];
        foreach (scandir($groupDir) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }
            // 扫描每个模块的init目录，找出init类文件
            $initDir = $groupDir . '/' . $module . '/src/init';
            if (!is_dir($initDir)) {
                continue;
            }

            foreach (scandir($initDir) as $name) {
                if ($name === '.' || $name === '..') {
                    continue;
                }

                $filename = pathinfo($initDir . '/' . $name, PATHINFO_FILENAME);
                $initClassName = '\\liansu\\init\\' . $filename;
                if (!class_exists($initClassName)) {
                    continue;
                }

                if (!(new $initClassName() instanceof IRun)) {
                    continue;
                }

                $initMapping[$module][] = $initClassName;
            }
        }

        // 按优先级加载
        // core > core_plus > api
        foreach (['core', 'core_plus', 'api', 'framework'] as $module) {
            if (empty($initMapping[$module])) {
                continue;
            }
            $this->init(...$initMapping[$module]);
            unset($initMapping[$module]);
        }

        foreach ($initMapping as $module => $inits) {
            $this->init(...$inits);
            unset($initMapping[$module]);
        }
    }
}
