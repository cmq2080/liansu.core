<?php

namespace liansu\core\traits;

trait TAppDefault
{
    private $defaultRunner = 'index';
    private $defaultAction = 'index';

    public function setDefaultRunner($defaultRunner)
    {
        if ($defaultRunner) {
            $this->defaultRunner = $defaultRunner;
        }

        return $this;
    }

    public function getDefaultRunner()
    {
        return $this->defaultRunner;
    }

    public function setDefaultAction($defaultAction)
    {
        if ($defaultAction) {
            $this->defaultAction = $defaultAction;
        }

        return $this;
    }

    public function getDefaultAction()
    {
        return $this->defaultAction;
    }
}
