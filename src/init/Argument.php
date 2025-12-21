<?php

namespace liansu\init;

use liansu\interfaces\ISortableRun;

class Argument implements ISortableRun
{
    public function run()
    {
        // 初始化参数
        \liansu\facade\Argument::initialize();
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
