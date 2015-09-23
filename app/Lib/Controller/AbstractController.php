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
        $arguments["current_user"] = $this->getCurrentUser();

        return new Response( $this->container->get('twig')->getTwig()->render($htmlFile, $arguments));
    }


    public function getCurrentUser(){

        if(NULL!=$this->getContainer()->get('security.context')->getSecurityContext()->getToken()){
            
            return $this->getContainer()->get('security.context')->getSecurityContext()->getToken()->getUser();
        }
    }

}