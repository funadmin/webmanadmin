<?php

namespace Webman;

use Psr\Container\ContainerInterface;
use Webman\Exception\NotFoundException;

/**
 * Class Container
 * @package Webman
 */
class Container implements ContainerInterface
{

    /**
     * @var array
     */
    protected $_instances = [];

    /**
     * @param string $name
     * @return mixed
     * @throws NotFoundException
     */
    public function get(string $name)
    {
        if (!isset($this->_instances[$name])) {
            if (!\class_exists($name)) {
                throw new NotFoundException("Class '$name' not found");
            }
            $this->_instances[$name] = new $name();
        }
        return $this->_instances[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->_instances);
    }

    /**
     * @param string $name
     * @param array $constructor
     * @return mixed
     * @throws NotFoundException
     */
    public function make(string $name, array $constructor = [])
    {
        if (!\class_exists($name)) {
            throw new NotFoundException("Class '$name' not found");
        }
        return new $name(... array_values($constructor));
    }

}
