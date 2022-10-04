<?php

/**
 * 描述：
 * Created at 2021/5/30 21:24 by mq
 */

namespace liansu\core;


use liansu\core\interface_\RunInterface;

class App implements RunInterface
{
    protected static $instance = null;

    // 五大公有变量
    public $rootDirectory = '';
    public $publicDirectory = '';
    public $configDirectory = '';
    public $runtimeDirectory = '';
    public $vendorDirectory = '';

    protected $routeParamName = 'r';
    protected $baseNamespace = '';
    protected $namespacePool = [];
    protected $initItems = [
        \liansu\core\init\Index::class,
    ];

    protected $configFiles = [];
    protected $tmpConfigs = [];

    protected $defaultApp = 'runner';
    protected $defaultAction = 'run';

    protected $_runner = '';
    protected $_action = '';

    public static function instance($configFile = null)
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        if (is_string($configFile) === true) {
            self::$instance->configFiles[] = $configFile;
        } else if (is_array($configFile) === true) {
            self::$instance->configFiles = array_merge(self::$instance->configFiles, $configFile);
        } else if (is_callable($configFile) === true) {
            self::$instance->tmpConfigs = ($configFile)();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        // // 定义常量们
        // defined('PUBLIC_DIRECTORY') || define('PUBLIC_DIRECTORY', realpath(PHP_SAPI === 'cli' ? __DIR__ . '/../../../../public' : $_SERVER['DOCUMENT_ROOT'])); // 如果是cli模式，怎么办？
        // defined('ROOT_DIRECTORY') || define('ROOT_DIRECTORY', realpath(PUBLIC_DIRECTORY . DIRECTORY_SEPARATOR . '..'));
        // //        defined('CONFIG_DIRECTORY') || define('CONFIG_DIRECTORY', realpath(ROOT_DIRECTORY . '/config'));
        // defined('VENDOR_DIRECTORY') || define('VENDOR_DIRECTORY', ROOT_DIRECTORY . DIRECTORY_SEPARATOR . 'vendor');
        // defined('RUNTIME_DIRECTORY') || define('RUNTIME_DIRECTORY', ROOT_DIRECTORY . DIRECTORY_SEPARATOR . 'runtime');

        // 初始化变量们
        $this->publicDirectory = realpath(PHP_SAPI === 'cli' ? __DIR__ . '/../../../../public' : $_SERVER['DOCUMENT_ROOT']);
        $this->rootDirectory = realpath($this->publicDirectory . '/..');
        $this->configDirectory = $this->rootDirectory . '/config';
        $this->vendorDirectory = $this->rootDirectory . '/vendor';
        $this->runtimeDirectory = $this->rootDirectory . '/runtime';
    }

    public function run()
    {
        try {
            // 运行之前要做的事
            $this->beforeRun();

            /************开始运行************/
            // 接收参数
            $app = Request::all($this->routeParamName);
            if (!$app) {
                $app = $this->defaultApp;
            }
            if (!$app) {
                throw new \Exception('No App Found');
            }

            // 初始化配置
            Config::setConfigFiles($this->configFiles);
            Config::set($this->tmpConfigs);
            if ($this->defaultAction) {
                Route::setDefaultAction($this->defaultAction);
            }

            // 找寻路由
            $app = Route::find($app);

            // 解析路由
            $runner = Route::parseStr($app);

            $find = false;
            foreach ($this->namespacePool as $namespace) {
                $testRunner = $namespace . '\\' . $runner;
                if (class_exists($testRunner)) {
                    $runner = $testRunner;
                    $find = true;
                    break;
                }
            }

            if (!$find) {
                throw new \Exception('runner不存在：' . $runner);
            }
            $action = Route::parseStr($app, 'action');

            // 实例化控制器类并执行动作
            Route::execute($runner, $action);
        } catch (\Exception $e) {
            Helper::showExceptionTrace($e);
        }
    }

    public function setBaseNamespace($baseNamespace)
    {
        if ($baseNamespace) {
            $this->baseNamespace = $baseNamespace;
        }

        return $this;
    }

    public function getBaseNamespace()
    {
        return $this->baseNamespace;
    }

    public function setRouteParamName($routeParamName)
    {
        if ($routeParamName) {
            $this->routeParamName = $routeParamName;
        }

        return $this;
    }

    public function getRouteParamName()
    {
        return $this->routeParamName;
    }

    public function setDefaultApp($app)
    {
        if ($app) {
            $this->defaultApp = $app;
        }

        return $this;
    }

    public function getDefaultApp()
    {
        return $this->defaultApp;
    }

    public function setDefaultAction($action)
    {
        if ($action) {
            $this->defaultAction = $action;
        }

        return $this;
    }

    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    public function addInitItems(...$initItems)
    {
        foreach ($initItems as $initItem) {
            if (is_array($initItem) === true) {
                foreach ($initItem as $item) {
                    $this->addInitItems($item);
                }
            } else {
                $this->initItems[] = $initItem;
            }
        }
    }

    protected function runInitItems()
    {
        foreach ($this->initItems as $initItem) {
            $initItem = str_replace('/', '\\', $initItem);
            if (class_exists($initItem) === false) {
                throw new \Exception('初始化类不存在');
            }
            (new $initItem())->run();
        }
    }

    protected function addNamespace($namespace)
    {
        $this->namespacePool[] = $namespace;
    }

    protected function beforeRun()
    {
        $this->runInitItems();
        array_unshift($this->namespacePool, $this->baseNamespace);
    }

    public function setRunner($runner)
    {
        if ($runner) {
            $this->_runner = $runner;
        }

        return $this;
    }

    public function getRunner()
    {
        return $this->_runner;
    }

    public function setAction($action)
    {
        if ($action) {
            $this->_action = $action;
        }

        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }
}
