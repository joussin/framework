<?php

namespace App\Lib;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class Controller
 * @package App\Lib
 */
abstract class Controller{

    private $container;

    public function __construct(){

//        $pimple['routes'] = function () {
//            $locator = new FileLocator(array(ROOT_PATH."/config"));
//            $loader = new YamlFileLoader($locator);
//            return $collection = $loader->load('routing.yml');
//        };

        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('../config/services.yml');

        $this->container =  $container;

    }
    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }


    /**
     * @param $htmlFile
     * @param $arguments
     * @return Response
     */
    public function render($htmlFile,$arguments){
        return new Response( $this->container->get('twig')->getTwig()->render($htmlFile, $arguments));
    }

}