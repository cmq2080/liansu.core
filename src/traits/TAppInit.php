<?php

namespace liansu\traits;

use liansu\interfaces\IRun;
use liansu\interfaces\ISortableRun;

trait TAppInit
{
    protected $inits = [];

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
                $this->inits[] = new class ($item) implements IRun {
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

    protected function autoloadInits()
    {
        $groupDir = VENDOR_DIRECTORY . '/liansu';
        $initArray = [];

        $initCache = sys_cache(2, 'inits'); // 从系统缓存中获取
        if ($initCache === false) { // 没有缓存则扫目录
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

                    $object = new $initClassName();
                    if (!($object instanceof IRun)) {
                        continue;
                    }

                    $sort = 1 << 30; // 默认为2的30次方
                    if ($object instanceof ISortableRun) {
                        if ($object->getSort() < 0 || $object->getSort() > ((1 << 31) - 1)) {
                            throw new \Exception('Invalid Sort Value: ' . $initClassName . '.(Range: 0 ~ 2^31-1)');
                        }
                        $sort = $object->getSort();
                    }

                    $initArray[] = ['module' => $module, 'class_name' => $initClassName, 'sort' => $sort];
                }
            }

            // 按顺序排序，sort越小越靠前
            for ($i = 0; $i < count($initArray); $i++) {
                for ($j = count($initArray) - 1; $j > $i; $j--) {
                    if ($initArray[$i] > $initArray[$j]) {
                        $tmp = $initArray[$i];
                        $initArray[$i] = $initArray[$j];
                        $initArray[$j] = $tmp;
                    }
                }
            }

            // 存入临时文件
            sys_cache(1, 'inits', json_encode($initArray));
        } else { // 有缓存则直接传数组
            $initArray = json_decode($initCache, true) ?: [];
        }

        foreach ($initArray as $init) {
            $this->init($init['class_name']);
        }
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
