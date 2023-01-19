<?php

namespace Apility\Plugins\Concerns;

use ReflectionMethod;
use RuntimeException;

trait BootableTraits
{
    protected $booted = [];

    /**
     * @throws RuntimeException
     */
    protected function bootTraits()
    {
        foreach (class_uses($this) as $trait) {
            if ($this->isTraitBootable($trait)) {
                $this->bootTraitIfNotBooted($trait);
            }
        }
    }

    public function getBootableTraitMethod(string $trait): string
    {
        return 'boot' . class_basename($trait);
    }

    protected function isTraitBootable(string $trait): bool
    {
        $method = $this->getBootableTraitMethod($trait);

        return method_exists(static::class, $method);
    }

    /**
     * @param string $trait
     * @throws RuntimeException
     */
    protected function bootTraitIfNotBooted(string $trait)
    {
        if (!in_array($trait, $this->booted)) {
            $this->bootTrait($trait);
        }
    }

    /**
     * @param string $trait
     * @param ReflectionMethod $reflectionMethod
     * @return array
     * @throws RuntimeException
     */
    protected function getBootableTraitMethodDependencies(string $trait, ReflectionMethod $reflectionMethod): array
    {
        $parameters = [];
        $class = static::class;
        $dependencies = $reflectionMethod->getParameters();

        if (count($dependencies)) {
            if (count($dependencies) > 1) {
                throw new RuntimeException("Bootable method [{$reflectionMethod->getName()}] defined in [$trait] can only have one or fewer parameter");
            }

            $reflectionParameter = $reflectionMethod->getParameters()[0];

            if ($type = $reflectionParameter->getType()) {
                $typeName = $type->getName();

                if (!($this instanceof $typeName)) {
                    throw new RuntimeException("Bootable method [{$reflectionMethod->getName()}] requires a parameter of type [{$type->getName()}] but [{$class}] isn't compatible");
                }
            }

            $parameters[] = $this;
        }

        return $parameters;
    }

    /**
     * @param string $trait
     * @throws RuntimeException
     */
    protected function bootTrait(string $trait)
    {
        $method = $this->getBootableTraitMethod($trait);
        $reflectionMethod = new ReflectionMethod($this, $method);

        if (!$reflectionMethod->isPublic()) {
            throw new RuntimeException("Bootable method [$method] defined in [$trait] must be public");
        }

        if (!$reflectionMethod->isStatic()) {
            throw new RuntimeException("Bootable method [$method] defined in [$trait] must be static");
        }

        $reflectionMethod->invokeArgs(null, $this->getBootableTraitMethodDependencies($trait, $reflectionMethod));

        $this->booted[] = $trait;
    }

    /**
     * @throws RuntimeException
     */
    public function boot()
    {
        $this->bootTraits();
    }
}
