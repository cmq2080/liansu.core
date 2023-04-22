<?php

namespace liansu\core;

class Route
{
    private static $routeParam = 'r';
    private static $routeDelimiter = '@';
    private static $defaultRunner;
    private static $defaultAction;

    public static function initialize()
    {
        self::$defaultRunner = App::instance()->getDefaultRunner();
        self::$defaultAction = App::instance()->getDefaultAction();
        if ($routeParam = Config::get('app.route_param')) {
            self::$routeParam = $routeParam;
        }
    }

    public static function parse()
    {
        $delimiter = self::$routeDelimiter;
        $r = Request::all(self::$routeParam, self::$defaultRunner . $delimiter . self::$defaultAction);
        if (!$r) {
            throw new \Exception('No Route Found');
        }

        $r = str_replace('/', '\\', $r);
        $r = trim($r, '\\');

        $runner = (explode($delimiter, $r)[0] ?? null) ?: self::$defaultRunner;
        $action = (explode($delimiter, $r)[1] ?? null) ?: self::$defaultAction;

        return ['runner' => $runner, 'action' => $action];
    }
}
