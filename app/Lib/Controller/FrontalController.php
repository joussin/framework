<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 06/09/15
 * Time: 18:36
 */
namespace App\Lib\Controller;

use App\Lib\Security\AuthenticationListener;

use App\Lib\Security\FirewallListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class FrontalController{



    public function __construct(){




        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/app/config'));
        $loader->load('services.yml');
        $loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/src/config'));
        $loader->load('services.yml');



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



        //PROVIDER
        $providerKey = "my_security_str";
        $anonymousKey = uniqid();
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
        //PROVIDER MANAGER
        $providers = array($provider, new AnonymousAuthenticationProvider($anonymousKey));
        $authenticationManager = new AuthenticationProviderManager($providers);



        //ACCES MANAGER
        $roleVoter = new RoleVoter('ROLE_');
        $voters = array($roleVoter);
        $accessDecisionManager = new AccessDecisionManager(
            $voters
        );


        $container->register('security.context', 'Symfony\Component\Security\Core\SecurityContext')
            ->addArgument($authenticationManager)
            ->addArgument($accessDecisionManager);



        $dispatcher->addSubscriber(new AuthenticationListener(
            $providerKey,
            $anonymousKey,
            $authenticationManager,
            $roleVoter,
            $container
        ));


        $dispatcher->addSubscriber(new FirewallListener(
            $container,
            $firewall,
            $matcher
        ));



//--------------------------------------------------------------


        $dispatcher->addSubscriber(new RouterListener($matcher));


        $container->compile();
        $resolver = new MyControllerResolver($container);

        $kernel = new HttpKernel($dispatcher,$resolver);

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);



    }



}