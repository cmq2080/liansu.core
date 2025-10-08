<?php

/**
 * 描述：
 * Created at 2021/5/30 21:44 by mq
 */

namespace liansu;

use liansu\exception\LiansuException;
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
        if ($this->isCli()) {
            $this->initArguments();
        }
    }

    /**
     * 依照方式获取数据
     * @param string $method
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function _get($method, $key = null, $default = null)
    {
        $tmpData = [];
        switch ($method) {
            case 'GET':
                $tmpData = $_GET;
                break;
            case 'POST':
                $tmpData = $_POST;
                break;
            case 'ARGS':
                $tmpData = $this->args;
                break;
            case 'ALL':
                $tmpData = array_merge($_REQUEST, $this->args);
                break;
            default:
                throw new LiansuException("Method Of \liansu\Request {$method} Is Invalid");
        }

        return empty($key) ? $tmpData : ($tmpData[$key] ?? $default);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        $name = strtoupper($name);
        if (in_array($name, ['GET', 'POST', 'ARGS', 'ALL']) === true) {
            // cli模式下不支持get、post方法
            if ($this->isCli() && in_array($name, ['GET', 'POST'])) {
                throw new LiansuException("Method Of \liansu\Request {$name} Is Invalid In CLI Mode");
            }
            // 非cli模式下不支持args方法
            if (!$this->isCli() && in_array($name, ['ARGS'])) {
                throw new LiansuException("Method Of \liansu\Request {$name} Is Only In CLI Mode");
            }

            return $this->_get($name, $arguments[0] ?? null, $arguments[1] ?? null);
        } else if (in_array($name, ['ISGET', 'ISPOST'])) {
            $method = substr($name, 2);

            return $this->isMethod($method);
        }

        return null;
    }

    /**
     * 功能：是否以Cli模式运行
     * Created at 2021/8/22 21:35 by mq
     * @return bool
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    private function initArguments()
    {
        $this->args = Argument::get();
    }

    /**
     * 获取请求方式
     * @return string|bool
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'] ?? false;
    }

    /**
     * 当前请求方式是否是xx方式
     * @param string $method
     * @return bool
     */
    public function isMethod($method): bool
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST'])) {
            return false;
        }
        return $this->getMethod() === $method;
    }

    public function isAjax(): bool
    {
        if (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            return true;
        }

        return false;
    }
}
