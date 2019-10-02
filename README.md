[![GitHub license](https://img.shields.io/badge/license-Apache%202-blue)](https://github.com/constanze-standard/container/blob/master/LICENSE)

## 我们只做专一的工作
Constanze Standard: Container 是一个简单专一的 [PSR-11](https://www.php-fig.org/psr/psr-11) 容器组件，它提供容器化解决方案所需的基础功能和基本组件，包括：容器（`Container`）和服务提供接口（`Service Provider`）。

## 如何获取组件？
请使用 [`composer`](https://getcomposer.org/) 安装组件，这可能需要几分钟的时间。
```
composer require constanze-standard/container
```

## 主要功能
1. Container 是符合主流标准的，它是 Fig PSR-11 的一种实现。
2. Entry Providers 允许你针对 container 打包你的代码或配置，以便重复使用，并在一定程度上避免不必要的 entry 定义。
3. Make Factory 强化了控制反转的功能，使你可以根据需要通过容器创建新实例，现在你可以将对象的实例化工作完全交给容器了。

## 案例演示

### 添加和获取
通过案例演示，有助于我们研究和理解 Container 的工作方式，现在我们向 Container 中，添加一个名为 `foo` 的静态内容（`bar`），然后再将内容从 Container 中取出。
```php
<?php declare(strict_types=1);

use ConstanzeStandard\Container\Container;
use ConstanzeStandard\Container\Entry;

$container = new Container;

$entry = new Entry('foo', 'bar');
$container->addEntry($entry);

$foo = $container->get('foo');
var_dump($foo);  // foo
```
以上是 Container 的一个基本使用场景，当我们从 Container 获取内容（`entry`）时，默认情况下，容器会将传入的内容原封不动的返回。

像大部分容器组件一样，Container 也支持 `entry` 的 `definition`, definition 是一个内容的定义，它是一个返回 entry 的可调用对象，其中定义了 entry 的创建步骤。当 get 方法被调用时，Container 会先通过 definition 对象生成 entry，然后再将 entry 放入容器并返回。
```php
$entry = new Entry('foo', function () {
    return 'bar';
}, true);
$container->addEntry($entry);

$foo = $container->get('foo');
var_dump($foo);  // foo
```
Entry 构造方法的第三个参数设为 `true` 表示该 entry 是一个 definition（默认为 false）。

你也可以使用 Container 的 add 方法创建和添加 entry，它的参数与 Entry 的构造方法相同，并会返回创建的 entry 对象。以上示例的也可以用 add 方法完成。
```php
$container->add('foo', function () {
    return 'bar';
}, true);

$foo = $container->get('foo');
var_dump($foo);  // foo
```
实际上，add 方法内部分别做了与前两例相似的操作，这是一种便利且被经常使用的代理方法。

### 对 Entry 使用别名
我们可以为一个 entry 定义多个不同的别名，Container 将保存这些别名，并将它们指向同一个 entry.
```php
$container->alias('aliasName', 'entryName');
```
从 Container 中以 `aliasName` 获取的值 与 `entryName` 的内容是相同的。

### 判断和删除操作
Container 支持 PSR-11 中所定义的 has 方法，用来判断一个 entry 是否存在。同时 Container 也支持删除指定名称的 entry 的 `remove` 方法。
```php
$container->has('foo');  // 判断名为 foo 的 entry 是否存在
$container->remove('foo');  // 从 Container 中删除名为 foo 的 entry
```

## Definition
Definition 是很常用的功能，下面介绍一些与 Definition 相关的功能和要点。

### 实例化工厂 Make Factory
Container 的 `make` 方法允许你控制 entry 的获取方式，在通常情况下，我们用 get 方法获取的内容是全局唯一的，也就是说，entry 只会被创建一次，当我们再次获取时，Container 会直接将之前保存的值返回。如果我们希望重新生成 entry，就可以使用 make 方法。
```php
$container->add('foo', function($foo) {
    return $foo;
}, true);

$result = $container->make('foo', 'FOO VALUE');
var_dump($result);  // FOO VALUE
```
make 方法允许你为 Definition 添加任意数量的`自定义参数`，但需要注意的是，如果自定义参数没有默认值的话，用 get 方法获取时会出现错误。

make 方法在对服务创建的控制反转（IoC）中有极大的作用，它使容器有机会去控制那些非单例的对象的创建，通过依赖查找的方式使系统中所有的对象创建工作，全部移交给容器负责。

### 静态绑定参数
有时，一些 definition 需要额外的数据源用来生成 entry，这时，我们可以通过 `addArguments` 方法为 definition  提前绑定一些`静态参数`。
```php
$container->add('foo', function ($container, $customArg) {
    return 'bar';
}, true)->addArguments($container);

$container->make('foo', 'CustomArg');
```
`addArguments` 方法接受任意数量的参数，这些参数将在 definition 被调用时直接作为参数按顺序传入。这些参数将排在自定义参数之前。

需要注意的是，当我们使用 make 方法时，不需要传递静态参数，只需要按顺序传递自定义参数就可以了。
