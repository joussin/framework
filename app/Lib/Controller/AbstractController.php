<?php

namespace App\Lib\Controller;

use App\Lib\Helper\HtmlHelper;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class Controller
 * @package App\Lib
 */
abstract class AbstractController{

    private $container;

    public function __construct($container){

        //création du conteneur de sevice
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
    public function render($htmlFile,$arguments = array()){

        $arguments['HtmlHelper']= new HtmlHelper();

        return new Response( $this->container->get('twig')->render($htmlFile, $arguments));
    }

}

