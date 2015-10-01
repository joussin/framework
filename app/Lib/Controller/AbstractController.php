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

    public function __construct($container)
    {
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
    public function render($htmlFile,$arguments = array())
    {
        $arguments['helper']= $this->getContainer()->get('helper');
        return new Response( $this->container->get('twig')->render($htmlFile, $arguments));
    }

}

