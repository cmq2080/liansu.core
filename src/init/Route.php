<?php

namespace liansu\init;

use liansu\interfaces\ISortableRun;

class Route implements ISortableRun
{
    public function run()
    {
        // 初始化路由
        \liansu\facade\Route::initialize();
    }

    /**
     * 排序越小越靠前，优先级最高为0
     * 排序为0的init
     * @return int
     */
    public function getSort(): int
    {
        return 1;
    }
}
