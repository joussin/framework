<?php


namespace App\Lib\Security;


 use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
 use Symfony\Component\Security\Core\Exception\AccessDeniedException;
 use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;


class AuthListener implements EventSubscriberInterface
{

    //AUTH var

    public $matcher;

    public  function __construct($matcher){
        $this->matcher =$matcher;
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


            $userProvider = new InMemoryUserProvider(
                array(
                    'stef' => array(
                        // password is "password"
                        'password' => 'password',
                        'roles'    => array('ROLE_ADMIN'),
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



            $providers = array(
                $provider
            );

            $authenticationManager = new AuthenticationProviderManager($providers);

            try {
                $authenticatedToken = $authenticationManager->authenticate($unAuthToken);
                echo "etape 1: authentifié <br>";




                $roleVoter = new RoleVoter('ROLE_');
                $roleVoter->vote($authenticatedToken, new \stdClass(), array('ROLE_ADMIN'));
                $voters = array($roleVoter);
                $accessDecisionManager = new AccessDecisionManager(
                    $voters
                );

                $securityContext = new SecurityContext(
                    $authenticationManager,
                    $accessDecisionManager
                );
                $securityContext->setToken($authenticatedToken);



            } catch (AuthenticationException $failed) {
                // authentification a échoué

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
