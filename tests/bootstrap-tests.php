<?php

use PHPUnit\Framework\Assert;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Загружаем assert-функции из phpunit
require_once dirname((new ReflectionClass(Assert::class))->getFileName()) . '/Assert/Functions.php';
