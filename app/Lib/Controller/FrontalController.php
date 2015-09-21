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
use App\Lib\Security\FirewallListener;
use App\Lib\Security\MyChannelListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as YamlFileLoaderDic;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
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



use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\HttpUtils;

class FrontalController{



    public function __construct(){


        $container = new ContainerBuilder();
        $loader = new YamlFileLoaderDic($container, new FileLocator(ROOT_PATH.'/app/config'));
        $loader->load('services.yml');
        $loader = new YamlFileLoaderDic($container, new FileLocator(ROOT_PATH.'/src/config'));
        $loader->load('services.yml');



        $locator = new FileLocator(array(ROOT_PATH."/src/config"));
        $loader = new YamlFileLoader($locator);
        $routes = $loader->load('routing.yml');

        $request = Request::createFromGlobals();

        $matcher = new UrlMatcher($routes, new RequestContext());

        $dispatcher = new EventDispatcher();
        $resolver = new MyControllerResolver($container);

        $kernel = new HttpKernel($dispatcher,$resolver);
//--------------------------------------------------------------
//                    SECURITY
//--------------------------------------------------------------


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




        $requestMatcher = new RequestMatcher('^/admin/');
        $accessMap = new AccessMap($requestMatcher, array('ROLE_USER'));

        $httpUtils = new HttpUtils();
        $listeners = array(
//            new Firewall\AnonymousAuthenticationListener($container->get('security.context'), 'anonymousKey',null,$authenticationManager),
            new AuthListener($container->get('security.context'),$authenticationManager,$providerKey)
        );

        $exceptionListener = new ExceptionListener(
            $container->get('security.context'),
            // default implementation of the authentication trust resolver
            new AuthenticationTrustResolver('', ''), // $anonymousClass, $rememberMeClass
            // encapsulates the logic needed to create sub-requests, redirect the user, and match URLs.
            $httpUtils,
            ''
        );



        $map = new FirewallMap();
        $map->add($requestMatcher, $listeners, $exceptionListener);
        $firewall = new Firewall($map, $dispatcher);
        $dispatcher->addListener(KernelEvents::REQUEST, array($firewall, 'onKernelRequest'));



//--------------------------------------------------------------
        $dispatcher->addSubscriber(new RouterListener($matcher));
        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);


        var_dump("TOKEN =" . $container->get('security.context')->getToken());








    }



}