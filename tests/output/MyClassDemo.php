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
