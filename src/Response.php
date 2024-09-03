<?php

/**
 * 描述：
 * Created at 2021/5/30 21:45 by mq
 */

namespace liansu;

class Response
{
    const SUCCESS = 0;

    public function printf($result)
    {
        exit($result);
    }

    public function json($stat, $msg = '', $data = [])
    {
        header('Content-Type:application/json');
        $this->printf(json_encode(['stat' => $stat, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE));
    }

    public function success($msg = 'OK', $data = [])
    {
        $this->json(self::SUCCESS, $msg, $data);
    }

    public function error($msg = 'ERR', $data = [], $stat = 1)
    {
        $this->json($stat, $msg, $data);
    }
}
