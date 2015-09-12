<?php

namespace App\Lib;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 * @package App\Lib
 */
abstract class Controller{

    //conteneur de service
    private $container;

    public function __construct(){

//        $pimple['routes'] = function () {
//            $locator = new FileLocator(array(ROOT_PATH."/config"));
//            $loader = new YamlFileLoader($locator);
//            return $collection = $loader->load('routing.yml');
//        };


        $container = new ContainerBuilder();

        //service du coeur
        $container->register('parameters', 'App\Lib\Services\ParametersService');
        $container->register('twig', 'App\Lib\Services\TwigService');
        $container->setDefinition('doctrine', new Definition(
            'App\Lib\Services\DoctrineService',
            array(new Reference('parameters'))
        ));

        //service de mon application
        $container->register('myservice', 'Src\Services\MyService');

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