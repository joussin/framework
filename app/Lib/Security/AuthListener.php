<?php


namespace App\Lib\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;


class AuthListener implements EventSubscriberInterface
{

    //AUTH var
    public $firewall = array();






    public $matcher;
    public  function __construct($matcher){
        $this->matcher =$matcher;


        $this->firewall = array(
            "route_1"
        );


    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $parameters = $this->matcher->matchRequest($request);





        //on écoute si les infos de login son passé en POST
        if(
            NULL !== $request->request->get('_username') &&
            NULL !== $request->request->get('_password')
        ){

            $user = $request->request->get('_username');
            $pass = $request->request->get('_password');



            $providerKey = "mysecuritystr";



            //foruni par l'utilisateur
            $unAuthToken = new UsernamePasswordToken(
                $user,
                $pass,
                $providerKey
            );



            $userProvider = new InMemoryUserProvider(
                array(
                    'admin' => array(
                        // password is "foo"
                        'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
                        'roles'    => array('ROLE_ADMIN'),
                    ),
                )
            );

            // for some extra checks: is account enabled, locked, expired, etc.?
            $userChecker = new UserChecker();

            // an array of password encoders (see below)
            $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
            $encoders = array(
                'Symfony\\Component\\Security\\Core\\User\\User' => $defaultEncoder
            );
            $encoderFactory = new EncoderFactory($encoders);
            $provider = new DaoAuthenticationProvider(
                $userProvider,
                $userChecker,
                $providerKey,
                $encoderFactory
            );



            try {
                $authenticatedToken = $provider->authenticate($unAuthToken);
                echo "ok";
            } catch (AuthenticationException $failed) {
                // authentication failed
                echo "authentication failed";
            }



        }








        //comparaison avec la liste des routes du firewall
        if (
            in_array($parameters["_route"], $this->firewall)
        ){

            $parameters = array(
                "_controller"=> 'Src\Controllers\SecurityController::loginAction',
                "_route"=>  "security_login"
            );
            $request->attributes->add($parameters);
            $request->attributes->set('_route_params', $parameters);


        }





    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}
