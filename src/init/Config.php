<?php

namespace liansu\init;

use liansu\App;
use liansu\interfaces\ISortableRun;

class Config implements ISortableRun
{
    public function run()
    {
        // 初始化配置
        \liansu\facade\Config::initialize(App::instance()->getConfigFiles(), App::instance()->getTmpConfigs());
    }

    /**
     * 排序越小越靠前，优先级最高为0
     * 排序为0的init
     * @return int
     */
    public function getSort(): int
    {
        return 0;
    }
}
