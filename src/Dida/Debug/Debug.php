<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files MUST retain the above copyright notice.
 */

namespace Dida\Debug;

/**
 * Debug 类
 */
class Debug
{
    /**
     * Version
     */
    const VERSION = '20171114';


    /**
     * 显示一个需要跟踪的变量，然后停止运行
     *
     * 如果是想不显示变量就直接停止的话，建议用PHP自带的die()或者exit()。
     * 本类主要目的是Debug用途，函数设计时，重点考虑的是Debug时的方便。
     */
    public static function halt($var, $varname = null)
    {
        self::variable($var, $varname);
        exit();
    }


    /**
     * 显示一个需要跟踪的变量
     */
    public static function variable($var, $varname = null)
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<pre>' . htmlspecialchars(self::varExport($var, $varname)) . '</pre>';
    }


    /**
     * 导出变量
     */
    public static function varDump()
    {
        $result = [];

        $num = func_num_args();
        for ($i = 0; $i < $num; $i++) {
            $var = func_get_arg($i);
            $no = $i + 1;
            $result[] = "No.{$no} = " . self::formatVar($var);
        }

        return "\n" . implode("\n", $result) . "\n";
    }


    /**
     * 输出或返回一个变量的字符串表示
     *
     * @param mixed $var 变量
     * @param string $varname 变量名
     */
    public static function varExport($var, $varname = null)
    {
        // 如果不设置变量名，则等效于self::varDump()
        if (!is_string($varname) || $varname === '') {
            return self::formatVar($var);
        }

        // 变量名 = 变量值;
        $begin = $varname . ' = ';
        $leading = strlen($begin);
        $v = self::formatVar($var, $leading);
        $end = ';' . PHP_EOL;

        return $begin . $v . $end;
    }


    /**
     * 把一个变量的值，用可读性良好的格式进行输出
     *
     * @return string
     */
    protected static function formatVar($var, $leading = 0)
    {
        // 为 null
        if (is_null($var)) {
            return 'null';
        }

        // 为数组
        if (is_array($var)) {
            return self::formatArray($var, $leading);
        }

        // 其它类型
        return var_export($var, true);
    }


    /**
     * 把一个数组的值，用可读性良好的格式进行输出
     *
     * @param array $array
     * @param int $leading 前导空格的数量
     * @return string
     */
    protected static function formatArray($array, $leading = 0)
    {
        // 如果是空数组，直接返回[]
        if (empty($array)) {
            return '[]';
        }

        // 前导空格
        $leadingspaces = str_repeat(' ', $leading);

        // 找出名称最长的key
        $maxlen = 0;
        $keys = array_keys($array);
        $is_string_key = false;
        foreach ($keys as $key) {
            if (is_string($key)) {
                $is_string_key = true;
            }
            $len = mb_strwidth($key);
            if ($len > $maxlen) {
                $maxlen = $len;
            }
        }
        if ($is_string_key) {
            $maxlen = $maxlen + 2;
        }

        // 生成数组定义个每一行
        $s = [];
        $s[] = '['; // 第一行无需前导空格
        foreach ($array as $key => $value) {
            $key = (is_string($key)) ? "'$key'" : $key;
            $value = self::formatVar($value, $leading + $maxlen + 8);
            $s[] = sprintf("%s    %-{$maxlen}s => %s,", $leadingspaces, $key, $value);
        }
        $s[] = $leadingspaces . ']';    // 最后一行

        return implode(PHP_EOL, $s);
    }
}
