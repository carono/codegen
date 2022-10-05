<?php


namespace carono\codegen;


use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Class ClassGenerator
 *
 * @package carono\codegen
 */
abstract class ClassGenerator
{
    /**
     * External generator
     *
     * @var mixed
     */
    public $generator;

    public $namespace;
    public $className;
    public $extends;
    public $output;

    protected $params = [];
    /**
     * @var ClassType
     */
    protected $phpClass;
    /**
     * @var PhpNamespace
     */
    protected $phpNamespace;
    /**
     * @var PhpFile
     */
    protected $phpFile;
    protected $exceptRenderMethods = [
        'render',
        'renderToFile',
        'classUses',
        'classAfterRender',
        'classGeneratedBy',
        'formClassNamespace',
        'formClassName',
        'formExtends',
        'formOutputPath',
        'classTraits',
        'classImplements',
        'phpDocComments',
        'phpProperties',
        'classConstants',
        '__construct'
    ];

    /**
     * @return array
     */
    protected function classUses()
    {
        return [];
    }

    /**
     * @param $className
     * @return ClassType
     */
    protected function getPhpClass($className)
    {
        if (!$this->phpClass) {
            $this->phpFile = new PhpFile();
            $namespace = $this->phpFile->addNamespace($this->namespace ? $this->namespace : $this->formClassNamespace());
            $this->phpNamespace = $namespace;
            return $this->phpClass = $namespace->addClass($className);
        } else {
            return $this->phpClass;
        }
    }

    /**
     * @return void
     */
    protected function classAfterRender()
    {

    }

    /**
     * @return array
     */
    protected function classGeneratedBy()
    {
        return [
            'This class is generated using the package carono/codegen'
        ];
    }

    /**
     * @return null|string
     */
    protected function formClassNamespace()
    {
        return null;
    }

    /**
     * @return null|string
     */
    protected function formClassName()
    {
        return null;
    }

    /**
     * @return null|string
     */
    protected function formExtends()
    {
        return null;
    }

    /**
     * @param ClassType $class
     */
    protected function applyClassProperties($class)
    {
        foreach (array_filter($this->classProperties()) as $property => $value) {
            if (is_array($value)) {
                if (isset($value['value'])) {
                    $classProperty = $class->addProperty($property, $value['value']);
                } else {
                    $classProperty = $class->addProperty($property);
                }
                if (isset($value['visibility'])) {
                    $classProperty->setVisibility($value['visibility']);
                }
                if (isset($value['comment'])) {
                    foreach ((array)$value['comment'] as $comment) {
                        $classProperty->addComment($comment);
                    }
                }
            } else {
                $class->addProperty($property, $value);
            }
        }
    }

    /**
     * @param $params
     * @return string
     * @throws \Exception
     */
    public function render($params = [])
    {
        $this->params = $params;
        $className = $this->className ? $this->className : $this->formClassName();
        if (!$className) {
            throw new \Exception('The class name was not set, update $className parameter or implement formClassName()');
        }
        $class = $this->getPhpClass($className);
        if ($extends = ($this->extends ? $this->extends : $this->formExtends())) {
            $this->phpClass->setExtends($extends);
        }
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!in_array($method->name, $this->exceptRenderMethods)) {
                $result = call_user_func([$this, $method->name], $class->addMethod($method->name));
                if ($result === false) {
                    $methods = $class->getMethods();
                    unset($methods[$method->name]);
                    $class->setMethods($methods);
                }
            }
        }
        $this->applyClassProperties($class);
        foreach (array_filter($this->classUses()) as $alias => $namespace) {
            $this->phpNamespace->addUse($namespace, is_numeric($alias) ? null : $alias);
        }
        foreach ($this->phpDocComments() as $comment) {
            $this->phpClass->addComment($comment);
        }

        foreach (array_filter($this->classConstants()) as $constant => $value) {
            $this->phpClass->addConstant($constant, $value);
        }
        foreach (array_filter($this->classTraits()) as $trait => $resolutions) {
            $this->phpClass->addTrait(is_numeric($trait) ? $resolutions : $trait, is_numeric($trait) ? [] : $resolutions);
        }
        foreach (array_filter($this->classImplements()) as $implement) {
            $this->phpClass->addImplement($implement);
        }
        $this->classAfterRender();
        $generatedBy = $this->classGeneratedBy();
        $this->phpFile->addComment(is_array($generatedBy) ? join("\n", $generatedBy) : $generatedBy);
        $this->output = $this->formOutputPath();
        return (string)$this->phpFile;
    }

    /**
     * @return null|string
     */
    protected function formOutputPath()
    {
        return null;
    }

    /**
     * @param $filePath
     * @param $params
     * @return bool|int
     */
    public function renderToFile($params = [], $filePath = null)
    {
        $content = $this->render($params);
        return file_put_contents($filePath ?: $this->output, $content);
    }

    /**
     * @return array
     */
    protected function classTraits()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function classImplements()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function phpDocComments()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function classProperties()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function classConstants()
    {
        return [];
    }
}