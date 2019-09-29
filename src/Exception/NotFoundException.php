<?php

namespace ConstanzeStandard\Dependency\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
}
