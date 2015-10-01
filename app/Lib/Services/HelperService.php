<?php
/**
 * Created by PhpStorm.
 * User: wal21
 * Date: 01/10/15
 * Time: 13:14
 */

namespace App\Lib\Services;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

class HelperService{

    private $router;

    public function __construct($router){
        $this->router = $router;
    }

    /**
     * @return mixed
     */
    public static function getAssetDirectory()
    {
        return WEB_PATH;
    }

    /**
     * @param $filePath
     * @return string
     */
    public function asset($filePath)
    {
        return self::getAssetDirectory().$filePath;
    }

    /**
     * @param $filePath
     */
    public function css($filePath)
    {
        echo '<link href="'.self::getAssetDirectory().'/css/'.$filePath.'" rel="stylesheet" />';
    }

    /**
     * @param $filePath
     */
    public function script($filePath)
    {
        echo '<script src="'.self::getAssetDirectory().'/js/'.$filePath.'" type="text/javascript" ></script>';
    }

    /**
     * @param $data
     */
    public function dump($data)
    {
        return var_dump($data);
    }


    /**
     * @param $route_alias
     * @param $args
     * @return string
     */
    public function generateUrl($route_alias,$args=array()){
        $routes= $this->router->getRoutes();
        $context = new RequestContext(self::getAssetDirectory());
        $generator = new UrlGenerator($routes, $context);
        return $generator->generate($route_alias, $args);
    }





}