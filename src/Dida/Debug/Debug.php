<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License
 * Redistributions of files MUST retain the above copyright notice.
 */

namespace Dida\Debug;

class Debug
{
    public static function halt($var, $varname = null)
    {
        self::variable($var, $varname);
        exit();
    }


    public static function variable($var, $varname = null)
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<pre>' . htmlspecialchars(self::varExport($var, $varname)) . '</pre>';
    }


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


    public static function varExport($var, $varname = null)
    {
        if (!is_string($varname) || $varname === '') {
            return self::formatVar($var);
        }

        $begin = $varname . ' = ';
        $leading = strlen($begin);
        $v = self::formatVar($var, $leading);
        $end = ';' . PHP_EOL;

        return $begin . $v . $end;
    }


    protected static function formatVar($var, $leading = 0)
    {
        if (is_null($var)) {
            return 'null';
        }

        if (is_array($var)) {
            return self::formatArray($var, $leading);
        }

        return var_export($var, true);
    }


    protected static function formatArray($array, $leading = 0)
    {
        if (empty($array)) {
            return '[]';
        }

        $leadingspaces = str_repeat(' ', $leading);

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

        $s = [];
        $s[] = '[';
        foreach ($array as $key => $value) {
            $key = (is_string($key)) ? "'$key'" : $key;
            $value = self::formatVar($value, $leading + $maxlen + 8);
            $s[] = sprintf("%s    %-{$maxlen}s => %s,", $leadingspaces, $key, $value);
        }
        $s[] = $leadingspaces . ']';

        return implode(PHP_EOL, $s);
    }
}
