<?php

namespace liansu\traits;

trait TAppDefault
{
    protected $defaultRunner = 'index';
    protected $defaultAction = 'index';

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
