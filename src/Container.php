<?php
namespace liansu;
use liansu\exception\LiansuException;

class Container
{
    protected $data = [];

    public function get($applicationClassName, $newInstance = false)
    {
        if (!class_exists($applicationClassName)) {
            throw new LiansuException('Application Class Not Found: ', $applicationClassName);
        }

        if ($newInstance) {
            return $this->newInstance($applicationClassName);
        }

        $key = $this->getKey($applicationClassName);
        $instance = $this->data[$key] ?? $this->register($applicationClassName);

        return $instance;
    }

    public function newInstance($applicationClassName)
    {
        return new $applicationClassName();
    }

    public function getKey($applicationClassName)
    {
        $key = trim($applicationClassName, '\\');
        return $key;
    }

    public function register($applicationClassName, $instance = null)
    {
        $key = $this->getKey($applicationClassName);
        if (empty($instance)) {
            $instance = $this->newInstance($applicationClassName);
        }
        $this->data[$key] = $instance;
        return $instance;
    }
}