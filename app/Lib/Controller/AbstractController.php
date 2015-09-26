<?php

namespace App\Lib\Controller;

use Symfony\Component\HttpFoundation\Response;


/**
 * Class Controller
 * @package App\Lib
 */
abstract class AbstractController{

    private $container;
    private $asset_directory;

    public function __construct($container){

        //création du conteneur de sevice
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

    public function renderPhp($htmlFile,$arguments = array()){

        extract($arguments);
        ob_start();
        include($htmlFile);
        $out = ob_get_contents();
        ob_end_clean();

        return new Response( $out);
    }

}