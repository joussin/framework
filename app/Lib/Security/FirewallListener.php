<?php


namespace App\Lib\Security;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class FirewallListener implements EventSubscriberInterface
{

    public $protectedRoutes = array();

    public $matcher;

    public  function __construct($matcher){
        $this->matcher =$matcher;
        $this->protectedRoutes = array(
            "route_1"
        );

    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $parameters = $this->matcher->matchRequest($request);

        if (in_array($parameters["_route"], $this->protectedRoutes )){



            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Texte utilisé si le visiteur utilise le bouton d\'annulation';
                exit;
            } else {
                echo "<p>Bonjour, {$_SERVER['PHP_AUTH_USER']}.</p>";

            }

//            $parameters = array(
//                "_controller"=> 'Src\Controllers\SecurityController::loginAction',
//                "_route"=>  "security_login"
//            );
//            $request->attributes->add($parameters);
//            $request->attributes->set('_route_params', $parameters);
//            $request->attributes->set('name', "name");


        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}
