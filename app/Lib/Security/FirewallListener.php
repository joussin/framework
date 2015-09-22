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


    public function __construct($container,$firewall,$matcher){

        $this->container = $container;
        $this->firewall = $firewall;
        $this->matcher = $matcher;

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $parameters = $this->matcher->matchRequest($request);
        $current_route = $parameters['_route'];

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (   array_key_exists($current_route, $this->firewall['routes'])  ) {

            $role_necessaire = $this->firewall['routes'][$current_route];

            //il est authentifié: a t'il les droits nécessaire
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
    }




    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}