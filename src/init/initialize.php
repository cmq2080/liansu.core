<?php

namespace liansu\core\init;

use liansu\core\App;
use liansu\core\Argument;
use liansu\core\Config;
use liansu\core\interfaces\IRun;
use liansu\core\Request;
use liansu\core\Route;

class initialize implements IRun
{
    public function run()
    {
        // 初始化配置
        Config::initialize(App::instance()->getConfigFiles(), App::instance()->getTmpConfigs());
        // 初始化参数
        Argument::initialize();
        // 初始化请求
        Request::initialize();
        // 初始化路由
        Route::initialize();
    }
}
