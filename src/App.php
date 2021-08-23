<?php
/**
 * 描述：
 * Created at 2021/5/30 21:24 by mq
 */

namespace liansu\core;


use liansu\core\interface_set\RunInterface;

class App implements RunInterface
{
    private static $instance = null;
    private $routeParam = 'api';
    private $baseNamespace = '';
    private $initItems = [
        'liansu/core/Config',
        'liansu/core/Request',
        'liansu/core/Route',
    ];

    private $configFiles = [];
    private $tmpConfigs = [];

    private $defaultApp = '';
    private $defaultAction = '';

    private $_runner = '';
    private $_action = '';

    public static function instance($configFile = null)
    {
        if (self::$instance === null) {
            self::$instance = new self();
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

    private function __construct()
    {
        // 定义常量们
        defined('PUBLIC_DIRECTORY') || define('PUBLIC_DIRECTORY', realpath($_SERVER['DOCUMENT_ROOT']));
        defined('ROOT_DIRECTORY') || define('ROOT_DIRECTORY', realpath(PUBLIC_DIRECTORY . '/..'));
//        defined('CONFIG_DIRECTORY') || define('CONFIG_DIRECTORY', realpath(ROOT_DIRECTORY . '/config'));
        defined('VENDOR_DIRECTORY') || define('VENDOR_DIRECTORY', realpath(ROOT_DIRECTORY . '/vendor'));
        defined('RUNTIME_DIRECTORY') || define('RUNTIME_DIRECTORY', realpath(ROOT_DIRECTORY . '/runtime'));

        // 初始化各个类
        $this->runInitItems();
    }

    public function run()
    {
        // 接收参数
        $app = Request::all($this->routeParam);
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
        $runner = $this->baseNamespace . '\\' . $runner;
        if (class_exists($runner) === false) {
            throw new \Exception('runner不存在：' . $runner);
        }
        $action = Route::parseStr($app, 'action');

        // 实例化控制器类并执行动作
        Route::execute($runner, $action);
    }

    public function setBaseNamespace($baseNamespace)
    {
        if ($baseNamespace) {
            $this->baseNamespace = $baseNamespace;
        }

        return $this;
    }

    public function setRouteParam($routeParam)
    {
        if ($routeParam) {
            $this->routeParam = $routeParam;
        }

        return $this;
    }

    public function setDefaultApp($app)
    {
        if ($app) {
            $this->defaultApp = $app;
        }

        return $this;
    }

    public function addInitItems(...$initItems)
    {
        foreach ($initItems as $initItem) {
            if (is_array($initItem) === true) {
                foreach ($initItem as $item) {
                    $this->initItems[] = $item;
                }
            } else {
                $this->initItems[] = $initItem;
            }
        }
    }

    private function runInitItems()
    {
        foreach ($this->initItems as $initItem) {
            $initItem = str_replace('/', '\\', $initItem);
            if (class_exists($initItem) === false) {
                throw new \Exception('初始化类不存在');
            }
            $initItem::initialize();
        }
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