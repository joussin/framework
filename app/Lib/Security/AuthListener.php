<?php


namespace App\Lib\Security;


 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
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
            $providerKey = $parameters["_route"];



            //foruni par l'utilisateur
            $unAuthToken = new UsernamePasswordToken(
                $user,
                $pass,
                $providerKey
            );



//            'password'
//            en md5:
//            5f4dcc3b5aa765d61d8327deb882cf99
//            en sha512
//            b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec049b46df5f1326af5a2ea6d103fd07c95385ffab0cacbc86


            $userProvider = new InMemoryUserProvider(
                array(
                    'stef' => array(
                        // password is "password"
                        'password' => 'password',
                        'roles'    => array('ROLE_USER'),
                    ),
                )
            );

            // for some extra checks: is account enabled, locked, expired, etc.?
            $userChecker = new UserChecker();

            $defaultEncoder = new PlaintextPasswordEncoder();
            $encoders = array(
                'Symfony\\Component\\Security\\Core\\User\\User' => $defaultEncoder,
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
//            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();

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
