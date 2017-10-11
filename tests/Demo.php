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

    protected function classProperties()
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