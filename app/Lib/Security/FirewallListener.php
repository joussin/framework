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


    public function __construct($container,$matcher){
        $this->container = $container;
        $this->firewall = $container->get('security.parameters')->getParameters()['firewall'];
        $this->matcher = $matcher;
        $this->routes =  $routes = $container->get('router')->getRoutes();

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $parameters = $this->matcher->matchRequest($request);
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

        $user = $this->container->get('security.context')->getSecurityContext()->getToken()->getUser();

        if( !$this->container->get('security.context')->getSecurityContext()->isGranted($role) ){

            if($user == 'anonymous'){
                $link = $this->container->get('router')->generateUrl('security_login');
                header('Location: '.$link);
                exit;
            }
            else{
                header('HTTP/1.0 403 Forbidden');
                die("403 Unauthorized");
            }
        }
    }




    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}