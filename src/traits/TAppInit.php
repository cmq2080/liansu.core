<?php

namespace liansu\traits;

use liansu\interfaces\IRun;

trait TAppInit
{
    protected $inits = [
        \liansu\init\Initialize::class,
    ];

    /**
     * 添加初始化组件
     */
    public function init(...$inits)
    {
        foreach ($inits as $item) {
            if (is_array($item)) { // 数组类型，继续分
                foreach ($item as $item2) {
                    $this->init($item2);
                }
            } else if (is_string($item)) { // 字符串类型，直接塞入（以后再实例化）
                $this->inits[] = $item;
            } else if (is_callable($item)) { // 回调方法，new一个匿名类对象包含它
                $this->inits[] = new class($item) implements IRun
                {
                    private $defaultFunc;
                    public function __construct(callable $item)
                    {
                        $this->defaultFunc = $item;
                    }

                    public function run()
                    {
                        $func = $this->defaultFunc;
                        $func();
                    }
                };
            } else if ($item instanceof IRun) { // 已经实例化的对象，直接塞入
                $this->inits[] = $item;
            } else {
                throw new \Exception('ERROR');
            }
        }

        return $this;
    }

    /**
     * 运行初始化组件
     */
    private function runInits()
    {
        foreach ($this->inits as $init) {
            if (!is_object($init)) {
                $init = new $init();
            }
            $init->run();
        }
    }
}
