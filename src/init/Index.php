<?php

namespace liansu\core\init;

use liansu\core\interface_\RunInterface;

/**
 * 初始化各个基础类
 */
class Index implements RunInterface
{
    public function run()
    {
        $targetDir = realpath(__DIR__ . '/../');

        foreach (scandir($targetDir) as $item) {
            $filepath = $targetDir . '/' . $item;
            if (is_dir($filepath)) {
                continue;
            }

            if (pathinfo($filepath, PATHINFO_FILENAME) === 'App') {
                continue;
            }

            $driver = '\\liansu\\core\\' . pathinfo($filepath, PATHINFO_FILENAME);
            $class = new \ReflectionClass($driver);
            if ($class->hasMethod('initialize') === true) {
                $method = $class->getMethod('initialize');
                if ($method->isStatic() === true) {
                    $driver::initialize();
                }
            }
        }
    }
}
