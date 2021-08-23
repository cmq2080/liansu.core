<?php
/**
 * 描述：
 * Created at 2021/6/6 22:58 by mq
 */

namespace liansu\core;


class Helper
{
    public static function str_replace($needle, $replace, $object)
    {
        if (is_array($needle) === true) {
            foreach ($needle as $value) {
                $object = str_replace($value, $replace, $object);
            }
        } else {
            $object = str_replace($needle, $replace, $object);
        }

        return $object;
    }
}