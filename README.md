# dida-debug

一个小巧的Debug，可以输出更易读的变量调试信息。

* MIT开源协议。
* 对Array型变量可以输出漂亮的组对齐格式。
* 对Array型变量的输出是用[...]，而不是用PHP内置的array(...)。

## 使用

用Composer安装：

```bash
composer require dida/debug
```

在PHP中使用：

```php
use Dida\Debug\Debug;

echo Debug::varDump($some_var);
```

## API

### `public static function halt($var, $varname = null)`

> 显示一个需要跟踪的变量，然后停止运行。
> 如果是想不显示变量就直接停止的话，建议用PHP自带的die()或者exit()。本类主要目的是Debug用途，函数设计时，重点考虑的是Debug时的方便。

### `public static function variable($var, $varname = null)`

> 显示一个需要跟踪的变量，和halt()类似，但是显示后，不会退出。

### `public static function varDump($var1, $val2, ...)`

> 导出变量。

### `public static function varExport($var, $varname = null)`

> 输出或返回一个变量的字符串表示。

## 关于

* 作者： [Macc Liu](https://github.com/maccliu)
* 感谢： [宙品科技](http://zeupin.com) , 尤其是 [Dida 项目团队](http://dida.zeupin.com)。

## 版权

Copyright (c) 2017 Zeupin LLC. Released under the [MIT license](LICENSE).