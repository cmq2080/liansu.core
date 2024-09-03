<?php

namespace liansu;

use liansu\facade\Config;
use liansu\facade\Request;

class Route
{
    private $routeParam;
    private $routeDelimiter;
    private $defaultRunner;
    private $defaultAction;

    public function initialize()
    {
        $this->defaultRunner = App::instance()->getDefaultRunner();
        $this->defaultAction = App::instance()->getDefaultAction();

        $this->routeParam = Config::get('app.route_param', 'r');
        $this->routeDelimiter = Config::get('app.route_delimiter', '@');
    }

    public function parse()
    {
        $delimiter = $this->routeDelimiter;
        $r = Request::all($this->routeParam, $this->defaultRunner . $delimiter . $this->defaultAction);
        if (!$r) {
            throw new \Exception('No Route Found');
        }

        $r = str_replace('/', '\\', $r);
        $r = trim($r, '\\');

        $runner = (explode($delimiter, $r)[0] ?? null) ?: $this->defaultRunner;
        $action = (explode($delimiter, $r)[1] ?? null) ?: $this->defaultAction;

        return ['runner' => $runner, 'action' => $action];
    }

    public function getRouteParam() // Q:变量总是在使用时才会创建空间，那么，类变量呢？
    {
        return $this->routeParam;
    }

    public function getRouteDelimiter()
    {
        return $this->routeDelimiter;
    }
}
