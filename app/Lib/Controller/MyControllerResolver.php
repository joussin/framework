<?php


namespace App\Lib\Controller;

 use Symfony\Component\HttpKernel\Controller\ControllerResolver;



class MyControllerResolver extends ControllerResolver
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns an instantiated controller.
     *
     * @param string $class A class name
     *
     * @return object
     */
    protected  function instantiateController($class)
    {
        return new $class($this->container);
    }
}
