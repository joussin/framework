<?php

namespace App\Lib\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class FirewallListener implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $container;

    /**
     * @var
     */
    private $firewall;

    /**
     * @var
     */
    private $matcher;

    private $routes;


    public function __construct($container,$firewall,$matcher,$routes){

        $this->container = $container;
        $this->firewall = $firewall;
        $this->matcher = $matcher;
        $this->routes = $routes;

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


    private function unauthorize($role_necessaire){

        $user = $this->container->get('security.context')->getToken()->getUser();

        if( !$this->container->get('security.context')->isGranted($role_necessaire) ){

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