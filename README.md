# Dida\Debug 组件库

`Dida\Debug` 一个小巧的Debug，以更加清晰易读的方式，输出调试变量信息。它是 [宙品科技](http://zeupin.com) 开源的 [Dida框架](http://dida.zeupin.com) 的一个功能组件库。

* MIT开源协议。
* 对Array型变量可以输出美观的组对齐格式。
* 对Array型变量的输出是用[...]，而不是用PHP内置的array(...)。

## 使用

用Composer安装：

```bash
composer require dida/debug
```

在PHP中使用：

```php
use Dida\Debug\Debug;

echo Debug::varDump($var1, $var2, ...);
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

## 版权声明

版权所有 (c) 2017 上海宙品信息科技有限公司。<br>Copyright (c) 2017 Zeupin LLC. <http://zeupin.com>

源代码采用MIT授权协议。<br>Licensed under The MIT License.

如需在您的项目中使用，必须保留本源代码中的完整版权声明。<br>Redistributions of files MUST retain the above copyright notice.
