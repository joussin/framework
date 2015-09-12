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

        if(DEV_MODE){
            $options = array(
                'cache' => false
            );
        }
        else{
            $options = array(
                'cache' => ROOT_PATH.'/app/cache/twig',
            );
        }

        $loader = new \Twig_Loader_Filesystem(
            array(
                ROOT_PATH.'/src/Views',
                ROOT_PATH.'/app/lib/Views',
            )
        );
        $this->twig = new \Twig_Environment($loader, $options);

    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }



}