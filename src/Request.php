<?php

/**
 * 描述：
 * Created at 2021/5/30 21:44 by mq
 */

namespace liansu;

use liansu\facade\Argument;

class Request
{
    private $args = [];

    /**
     * 功能：初始化
     * Created at 2021/8/22 16:12 by mq
     */
    public function initialize()
    {
        // TODO: Implement initialize() method.
        if ($this->isCli() === true) {
            $this->initArguments();
        }
    }

    private function _get($method, $key = null, $default = null)
    {
        if ($method === 'GET') {
            return $key === null ? $_GET : ($_GET[$key] ?? $default);
        } else if ($method === 'POST') {
            return $key === null ? $_POST : ($_POST[$key] ?? $default);
        } else if ($method === 'ARGS') {
            return $key === null ? $this->args : ($this->args[$key] ?? $default);
        } else if ($method === 'ALL') {
            $allData = array_merge($_REQUEST, $this->args);
            return $key === null ? $allData : ($allData[$key] ?? $default);
        }

        return $default;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        if (in_array(strtoupper($name), ['GET', 'POST', 'ARGS', 'ALL']) === true) {
            return $this->_get(strtoupper($name), $arguments[0] ?? null, $arguments[1] ?? null);
        }

        return null;
    }

    /**
     * 功能：
     * Created at 2021/8/22 21:35 by mq
     * @return bool
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    private function initArguments()
    {
        Argument::initialize();
        $this->args = Argument::get();
    }

    public function getMethod()
    {
        if ($this->isCli()) {
            return null;
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    public function isMethod($method): bool
    {
        if (!$this->isCli()) {
            return $this->getMethod() == strtoupper($method);
        }

        return false;
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isAjax(): bool
    {
        if (!$this->isCli()) {
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
                return true;
            }
        }

        return false;
    }
}
