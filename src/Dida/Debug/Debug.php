<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files MUST retain the above copyright notice.
 */

namespace Dida\Debug;

use \ReflectionObject;

class Debug
{
    const VERSION = '20171124';

    protected static $filter_prop_type = \ReflectionProperty::IS_PUBLIC;

    protected static $filter_prop_ignores = [];

    protected $objects = [];

    protected $objID = 0;


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


    public static function filterPropType($public = true, $protected = false, $private = false)
    {
        $flag = 0;
        if ($public) $flag = $flag | \ReflectionProperty::IS_PUBLIC;
        if ($protected) $flag = $flag | \ReflectionProperty::IS_PROTECTED;
        if ($private) $flag = $flag | \ReflectionProperty::IS_PRIVATE;

        self::$filter_prop_type = $flag;
    }


    public static function filterPropNames(array $ignores)
    {
        self::$filter_prop_ignores = $ignores;
    }


    public static function varDump()
    {
        $debug = new Debug();

        $result = [];
        $num = func_num_args();
        for ($i = 0; $i < $num; $i++) {
            $var = func_get_arg($i);
            $no = $i + 1;
            $result[] = "No.{$no} = " . $debug->formatVar($var);
        }

        return "\n" . implode("\n", $result) . "\n";
    }


    public static function varExport($var, $varname = null)
    {
        $debug = new Debug();

        if (!is_string($varname) || $varname === '') {
            return $debug->formatVar($var);
        }

        $begin = $varname . ' = ';
        $leading = strlen($begin);
        $v = $debug->formatVar($var, $leading);
        $end = ';' . PHP_EOL;

        return $begin . $v . $end;
    }


    protected function formatVar($var, $leading = 0)
    {
        if (is_null($var)) {
            return 'null';
        }

        if (is_array($var)) {
            return $this->formatArray($var, $leading);
        }

        if (is_object($var)) {
            return $this->formatObject($var, $leading);
        }

        return var_export($var, true);
    }


    protected function formatArray($array, $leading = 0)
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
            $value = $this->formatVar($value, $leading + $maxlen + 8);
            $s[] = sprintf("%s    %-{$maxlen}s => %s,", $leadingspaces, $key, $value);
        }
        $s[] = $leadingspaces . ']';

        return implode(PHP_EOL, $s);
    }


    protected function formatObject($obj, $leading = 0)
    {
        $r = new \ReflectionObject($obj);
        $className = $r->getName();

        if (isset($this->objects[$className])) {
            foreach ($this->objects[$className] as $uuid => $o) {
                if ($o === $obj) {
                    return "($className #$uuid) {...}";
                }
            }
            $uuid = $this->getNewObjID();
            $this->objects[$className][$uuid] = $obj;
        } else {
            $this->objects[$className] = [];
            $uuid = $this->getNewObjID();
            $this->objects[$className][$uuid] = $obj;
        }

        $leadingspace = str_repeat(' ', $leading);

        $output = [];
        $output[] = "($className #$uuid)";
        $output[] = $leadingspace . "{";


        $properties = $r->getProperties(self::$filter_prop_type);

        foreach ($properties as $property) {
            $propName = $property->getName();

            if ($this->ignored($className, $propName)) {
                continue;
            }

            $propStatic = ($property->isStatic()) ? '::' : '->';

            if ($property->isPublic()) {
                $propAccess = '';
            } elseif ($property->isProtected()) {
                $propAccess = '*';
            } elseif ($property->isPrivate()) {
                $propAccess = '!';
            }
            $propStr = "    {$propStatic}{$propName}{$propAccess} = ";

            $property->setAccessible(true);
            $propValue = $this->formatVar($property->getValue($obj), $leading + strlen($propStr));

            $output[] = "{$leadingspace}{$propStr}{$propValue}";
        }

        $output[] = $leadingspace . "}";

        return implode("\n", $output) . "\n";
    }


    protected function getNewObjID()
    {
        $this->objID ++;
        return $this->objID;
    }


    protected function ignored($class, $propName)
    {
        if (isset(self::$filter_prop_ignores[$class])) {
            return in_array($propName, self::$filter_prop_ignores[$class]);
        } else {
            return false;
        }
    }
}
