<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 06/09/15
 * Time: 18:36
 */
namespace App\Lib\Controller;

use App\Lib\Security\AuthenticationListener;
use App\Lib\Security\AuthListener;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class FrontalController{

    public $container;

    public function __construct(){

        global $container;
        $this->container = $container;

        $locator = new FileLocator(array(ROOT_PATH."/src/config"));
        $loader = new YamlFileLoader($locator);
        $routes = $loader->load('routing.yml');

        $request = Request::createFromGlobals();

        $matcher = new UrlMatcher($routes, new RequestContext());

        $dispatcher = new EventDispatcher();

//--------------------------------------------------------------


        //firwall
        $firewall = array(
            'secured_route_1'=>'ROLE_USER',
            'secured_route_2'=>'ROLE_ADMIN'
        );



        //CONFIGURATION
        $providerKey = "mysecuritystr";
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
        $providers = array($provider);
        $authenticationManager = new AuthenticationProviderManager($providers);



        //FOURNI PAR L'USER
        $user ="stef";
        $pass = "password";
        $unAuthToken = new UsernamePasswordToken(
            $user,
            $pass,
            $providerKey
        );


        //TRAITEMENT AUTHENTIFICATION
        $authenticatedToken = $authenticationManager->authenticate($unAuthToken);

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





        $parameters = $matcher->matchRequest($request);
        $current_route = $parameters['_route'];

        if (array_key_exists($current_route, $firewall)) {

            $role_necessaire = $firewall[$current_route];

            if (!$securityContext->isGranted($role_necessaire)) {

                $parameters = array(
                    "_controller"=> 'Src\Controllers\SecurityController::loginAction',
                    "_route"=>  "security_login"
                );
                $request->attributes->add($parameters);
                $request->attributes->set('_route_params', $parameters);

            }else{

                echo "VOUS ETES autorisé <br>";
            }

        }





//--------------------------------------------------------------


        $dispatcher->addSubscriber(new RouterListener($matcher));


        $resolver = new ControllerResolver();
        $kernel = new HttpKernel($dispatcher, $resolver);

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);

    }



}