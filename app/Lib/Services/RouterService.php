<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 19:20
 */
namespace App\Lib\Services;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

class RouterService{

    private $routes;

    public function __construct(){
        $locator = new FileLocator(array(ROOT_PATH."/app/config"));
        $loader = new YamlFileLoader($locator);
        $this->routes = $loader->load('routing.yml');
    }
    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }


}