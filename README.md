[![GitHub license](https://img.shields.io/badge/license-Apache%202-blue)](https://github.com/constanze-standard/container/blob/master/LICENSE)
[![Coverage 100%](https://img.shields.io/azure-devops/coverage/swellaby/opensource/25.svg)](https://github.com/constanze-standard/container)

## 我们只做专一的工作
Constanze Standard: Container 是一个简单专一的 [PSR-11](https://www.php-fig.org/psr/psr-11) 容器组件，它提供容器化解决方案所需的基础功能和基本组件，包括：容器（`Container`）和服务提供接口（`Service Provider`）。

## 如何获取组件？
请使用 [`composer`](https://getcomposer.org/) 安装组件，这可能需要几分钟的时间。
```
composer require constanze-standard/container
```

## 开始使用
引入并创建容器的实例：
```php
use Beige\Psr11\Container;

$container = new Container();
```

### 读写操作
可以在容器初始化的时候向容器写入数据，数据项的索引必须是字符串类型：
```php
$container = new Container([
    'foo' => 'bar'
]);
```

`Beige\Psr11\Container` 实现了 `ArrayAccess` 接口，支持以数组的形式操作数据：
```php
$container['foo'] = 'bar';
isset($container['foo']);  // true
unset($container['foo']);
```
如果根据索引无法找到数据，Container 会抛出一个 `Beige\Psr11\Exception\NotFoundException` 异常

### 定义 (Definition)
“定义”是一个可调用对象，它定义了生成一个数据的具体过程，并返回数据本身。容器可以利用“定义”来生成最终的数据。这里，我们使用 `Beige\Psr11\DefinitionCollection` 保存一个定义的集合，传入 container 中：
```php
$definitionCollection = new DefinitionCollection([
    'foo' => function($container) {
        return 'bar';
    }
]);

$container = new Container([], $definitionCollection);
$container['foo'];  // bar
```
`Beige\Psr11\Container` 构造函数的第二个参数可选，接受一个 `Beige\Psr11\Interfaces\DefinitionCollectionInterface` 接口的实例，当 Container 找不到某个索引的数据时，会查询 DefinitionCollection 中 对这个索引的“定义”，如果存在定义，容器就会利用定义生成具体数据并保存在容器中。Container 将延用 Definition 的索引。

`Beige\Psr11\DefinitionCollection` 与容器对象一样支持数组形式的操作：
```php
use Beige\Psr11\Container;
...

$definitionCollection['foo'] = function(Container $c) {
    return 'bar';
};
```

在默认情况下，Container 会把自身传给 Definition 的第一个参数，如上例所示，这样，你就可以在 Definition 内部获取容器中的其他数据，帮助当前的 Definition 生成数据。当然，你也可以定义一个“非常规”的 Definition，并用 `Beige\Psr11\Container::make` 方法生成数据：
```php
$definitionCollection['foo'] = function($num1, $num2) {
    return $num1 + $num2;
};

$container = new Container([], $definitionCollection);
$container->make('foo', [1, 2]);  // 3
```
`Beige\Psr11\Container::make` 方法的第一个参数是 Definition 的索引 _（注意：不是容器数据的索引）_；第二个参数是一个数组，是传给 Definition 的参数列表。 用 make 方法生成的数据会直接返回，且不会保存在容器中，也就是说，每调用一次 make 方法，都会重新生成一次数据！

使用 Definition 可以在需要时才生成数据，省却了不必要的消耗。推荐使用者将初始化消耗较大的数据转化为 Definition.
