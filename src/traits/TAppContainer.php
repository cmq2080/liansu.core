<?php

namespace liansu\traits;
use liansu\Container;

trait TAppContainer
{
    protected Container $container;

    protected function initializeContainer()
    {
        $this->container = new Container();
    }

    public function getContainerInstance($applicationClassName)
    {
        return $this->container->get($applicationClassName);
    }

    public function registerContainerInstance($applicationClassName, $instance = null)
    {
        $this->container->register($applicationClassName, $instance);
    }
}
