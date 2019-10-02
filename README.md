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

## 基本用法
通过案例演示，有助于我们研究和理解 Container 的工作方式，现在我们向 Container 中，以 `foo` 为索引添加一个静态内容 `bar`，然后再将内容从 Container 中取出。
```php
<?php declare(strict_types=1);

use ConstanzeStandard\Container\Container;

$container = new Container;

$container->add('foo', 'bar');

$foo = $container->get('foo');
var_dump($foo);  // foo
```
