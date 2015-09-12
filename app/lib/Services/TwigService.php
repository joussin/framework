<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

class TwigService{

    private $twig;

    public function __construct(){

        $loader = new \Twig_Loader_Filesystem(ROOT_PATH.'/src/Views');
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => ROOT_PATH.'/app/cache/twig',
        ));

    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }



}