<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 19:20
 */
namespace App\Lib\Services;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;

class RouterService{

    private $routes;


//    /**
//     * @param $route_alias
//     * @param $args
//     * @return string
//     */
//    public function generateUrl($route_alias,$args=array()){
//        $context = new RequestContext(WEB_PATH);
//        $generator = new UrlGenerator($this->routes, $context);
//        return $generator->generate($route_alias, $args);
//    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes()
    {
        //generation de routeCollection
        $locator = new FileLocator(array(ROOT_PATH."/app/config"));
        $loader = new YamlFileLoader($locator);

        $this->routes = $loader->load('routing.yml');
        return $this->routes;
    }





}