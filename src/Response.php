<?php

/**
 * 描述：
 * Created at 2021/5/30 21:45 by mq
 */

namespace liansu\core;


class Response
{
    const SUCCESS = 0;

    public static function printf($result)
    {
        exit($result);
    }

    public static function json($stat, $msg = '', $data = [])
    {
        header('Content-Type:application/json');
        self::printf(json_encode(['stat' => $stat, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE));
    }

    public static function success($msg = 'OK', $data = [])
    {
        self::json(self::SUCCESS, $msg, $data);
    }

    public static function error($msg = 'ERR', $data = [], $stat = 1)
    {
        self::json($stat, $msg, $data);
    }
}
