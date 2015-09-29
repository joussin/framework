<?php

namespace App\Lib\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FirewallListener implements EventSubscriberInterface
{

    private $firewall;

    private $matcher;

    private $routes;

    private $container;

    private $request;


    public function __construct($container,$matcher){
        $this->container = $container;
        $this->firewall = $container->get('security.parameters')['firewall'];
        $this->matcher = $matcher;
        $this->routes = $container->get('router')->getRoutes();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->request = $event->getRequest();

        $parameters = $this->matcher->matchRequest($this->request);
        $current_route = $parameters['_route'];
        $current_route_path = $this->routes->get($current_route)->getPath();

        if(preg_match("/".$this->firewall['pattern']['path']."/", $current_route_path)){
            $role_necessaire = $this->firewall['pattern']['role'];
            $this->unauthorize($role_necessaire);
        }
        else if ( array_key_exists($current_route, $this->firewall['routes']) ) {
            $role_necessaire = $this->firewall['routes'][$current_route];
            $this->unauthorize($role_necessaire);
        }
    }


    private function unauthorize($role){

        $token = $this->container->get('security')->getSecurityContext()->getToken();
        if($token === NULL){

//            $link = $this->container->get('router')->generateUrl('security_login');
//            header('Location: '.$link);
//            exit;

            $login_route_alias=  $this->firewall['login_route'];
            $login_route = $this->routes->get($login_route_alias);
            $parameters = array(
                "_controller"=> $login_route->getDefaults() ["_controller"],
                "_route"=>  $login_route_alias
            );
            $this->request->attributes->add($parameters);
            $this->request->attributes->set('_route_params', $parameters);
        }
        else if( !$this->container->get('security')->getSecurityContext()->isGranted($role) ){

            header('HTTP/1.0 403 Forbidden');
            die("403 Unauthorized");
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}