<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 06/09/15
 * Time: 18:36
 */
namespace App\Lib;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;


class FrontalController{



    public function __construct(){

        $this->getCurrentController();
    }

    public function getCurrentController(){


        $locator = new FileLocator(array(ROOT_PATH."/app/config"));
        $loader = new YamlFileLoader($locator);
        $routes = $loader->load('routing.yml');

        $request = Request::createFromGlobals();

        $matcher = new UrlMatcher($routes, new RequestContext());

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($matcher));

        $resolver = new ControllerResolver();
        $kernel = new HttpKernel($dispatcher, $resolver);

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);

    }

}