<?php

/**
 * 描述：
 * Created at 2021/5/30 21:45 by mq
 */

namespace liansu;

class Response
{
    const SUCCESS = 0;

    public function json($stat, $msg = '', $data = [])
    {
        return json_encode(['stat' => $stat, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    }

    public function success($data = [], $msg = 'OK')
    {
        return $this->json(self::SUCCESS, $msg, $data);
    }

    public function error($msg = 'ERR', $stat = 1, $data = [])
    {
        return $this->json($stat, $msg, $data);
    }
}
