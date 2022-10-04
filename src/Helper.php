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

    public static function url($runner, $action = null)
    {
        $paramName = App::instance()->getRouteParamName();
        if (!$action) {
            $action = App::instance()->getDefaultApp();
        }
        $url = '?' . $paramName . '=' . $runner . '@' . $action;
        return $url;
    }

    public static function showExceptionTrace(\Exception $e)
    {
        $html = '';
        // foreach ($e->getTrace() as $item) {
        //     $shtml = '<p>';
        //     $shtml .= $item['file'];
        //     $shtml .= '(' . $item['line'] . '): ';
        //     $shtml .=  $item['class'] . $item['type'] . $item['function'];
        //     if ($item['args'] ?? null) {
        //         foreach ($item['args'] as $key => $value) {
        //             if (is_string($value)) {
        //                 $value = '\'' . $value . '\'';
        //             } elseif (is_array($value)) {
        //                 $value = 'Array()';
        //             } elseif (is_object($value)) {
        //                 $value = 'Object()';
        //             } elseif (is_resource($value)) {
        //                 $value = 'Resource()';
        //             } elseif (is_callable($value)) {
        //                 $value = 'Callback()';
        //             } elseif (is_null($value)) {
        //                 $value = 'Null';
        //             } elseif (is_bool($value)) {
        //                 $value = 'Bool';
        //             }

        //             $item['args'][$key] = $value;
        //         }
        //     }
        //     $shtml .= '(' . implode(', ', $item['args']) . ')';
        //     $shtml .= '</p>';
        //     $html .= $shtml;
        // }
        $html .= '<h1>Running Wrong!!!</h1>';
        $html .= '<u>'.$e->getMessage() . '</u><br>';
        $html .= nl2br($e->getTraceAsString());
        Response::print($html);
    }
}
