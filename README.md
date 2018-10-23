[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/codegen/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/codegen/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/carono/codegen/v/stable)](https://packagist.org/packages/carono/codegen)
[![Total Downloads](https://poser.pugx.org/carono/codegen/downloads)](https://packagist.org/packages/carono/codegen)
[![License](https://poser.pugx.org/carono/codegen/license)](https://packagist.org/packages/carono/codegen)

Установка
=========
`composer require carono/codegen`

Как использовать
================
Создайте класс от `carono\codegen\ClassGenerator`

```php
<?php


namespace carono\codegen\tests;


use carono\codegen\ClassGenerator;
use Nette\PhpGenerator\Method;

class Demo extends ClassGenerator
{
    /**
     * @param Method $method
     */
    public function myFunc($method)
    {
        $method->addParameter('param1');
        $method->addParameter('param2', null);
        $method->addComment('@param mixed $param1');
        $method->addComment('@param mixed|null $param2');
        $method->addComment('@return mixed');
        $method->setStatic();
        $method->addBody('return ?;', [$this->params['value']]);
    }

    protected function phpProperties()
    {
        return ['id' => 100, 'name' => 'myParam'];
    }

    protected function phpDocComments()
    {
        return ['Auto generated class'];
    }

    protected function formExtends()
    {
        return 'ArrayObject';
    }

    protected function classConstants()
    {
        return [
            'const1' => 1,
            'const2' => 2
        ];
    }

    protected function classTraits()
    {
        return ['someTrait'];
    }

    protected function classUses()
    {
        return ['some\Object1', 'baseObject' => 'some2\Object1'];
    }

    protected function classAfterRender()
    {
        $property = $this->phpClass->addProperty('afterRenderProperty');
        $property->setStatic();
        $property->setVisibility('private');
        $property->addComment('Event after render');

        $method = $this->phpClass->addMethod('dynamicMethod' . $this->params['value']);
        $method->setStatic();
        $method->addBody('return ?;', [$this->params['value'] * 2]);
    }

    protected function formOutputPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR . 'MyClassDemo.php';
    }

    protected function formClassNamespace()
    {
        return 'carono\codegen\tests\Demo';
    }

    protected function formClassName()
    {
        return 'MyClassDemo';
    }
}
```
Создайте экземпляр своего генератора и произведите рендер

```php
<?php
require_once '../vendor/autoload.php';

require_once 'Demo.php';
$demo = new \carono\codegen\tests\Demo();
$content = $demo->render(['value' => 500]);
file_put_contents($demo->output, $content);
```

Итоговый файл
```php
<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace carono\codegen\tests\Demo;

use some2\Object1 as baseObject;
use some\Object1;

/**
 * Auto generated class
 */
class MyClassDemo extends \ArrayObject
{
	use \someTrait;

	const const1 = 1;
	const const2 = 2;

	public $id = 100;

	public $name = 'myParam';

	/** Event after render */
	private static $afterRenderProperty;


	/**
	 * @param mixed $param1
	 * @param mixed|null $param2
	 * @return mixed
	 */
	public static function myFunc($param1, $param2 = null)
	{
		return 500;
	}


	public static function dynamicMethod500()
	{
		return 1000;
	}
}
```

Все публичные методы в вашем генераторе, будут формировать аналогичные методы в итоговом классе. Приватные и защищенные методы игнорируются.

Более подробная информация по генератору можно найти на сайте источника https://packagist.org/packages/nette/php-generator
