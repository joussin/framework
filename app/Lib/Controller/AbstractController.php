<?php

namespace App\Lib\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class Controller
 * @package App\Lib
 */
abstract class AbstractController{

    private $container;
    private $asset_directory;

    public function __construct(){

        //création du conteneur de sevice
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/app/config'));
        $loader->load('services.yml');
        $loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/src/config'));
        $loader->load('services.yml');
        $container->compile();
        $this->container =  $container;

        //generation du repertoire des assets
        $this->asset_directory = WEB_PATH;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }
    /**
     * @return string
     */
    public function getAssetDirectory()
    {
        return $this->asset_directory;
    }
    /**
     * @param $htmlFile
     * @param $arguments
     * @return Response
     */
    public function render($htmlFile,$arguments = array()){

        $arguments["web_path"] = $this->getAssetDirectory();

        return new Response( $this->container->get('twig')->getTwig()->render($htmlFile, $arguments));
    }

}