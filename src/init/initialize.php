<?php

namespace liansu\init;

use liansu\App;
use liansu\facade\Argument;
use liansu\facade\Config;
use liansu\interfaces\IRun;
use liansu\facade\Request;
use liansu\facade\Route;

class Initialize implements IRun
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
