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

    private $container;

    private $routes;

    private $request;

    private $matcher;

    private $kernel;

    private $dispatcher;

    private $providerKey;


    public function __construct(){


        $this->initContainer();

        $this->initRoutes();

        $this->initKernel();



        //--------------------------------------------------------------
        //                    SECURITY
        //--------------------------------------------------------------





        //PROVIDER
        $anonymousKey = uniqid();
        $anonymousUsername = "anonymous";
        $anonymousProvider = new AnonymousAuthenticationProvider($anonymousKey);

        $inMemoryProvider = $this->getInMemoryProvider();

        //PROVIDER MANAGER
        $providers = array(
            $inMemoryProvider,
            $anonymousProvider
        );
        $authenticationManager = new AuthenticationProviderManager($providers);

        //ACCES DECISION MANAGER
        $roleVoter = new RoleVoter('ROLE_');
        $voters = array($roleVoter);
        $accessDecisionManager = new AccessDecisionManager(
            $voters
        );

        //CREATION DU SECURITY CONTEXT
        $this->container ->register('security.context', 'Symfony\Component\Security\Core\SecurityContext')
            ->addArgument($authenticationManager)
            ->addArgument($accessDecisionManager);


        //AJOUT D'UN ANONYMOUS TOKEN
        $anonymousToken = new AnonymousToken( $anonymousKey, $anonymousUsername, array());
        $authenticatedAnonymousToken = $authenticationManager->authenticate($anonymousToken);

            if($this->container->get("session")->get('security_token') === NULL){
                $this->container ->get('security.context')->setToken($authenticatedAnonymousToken);
            }else{
                $this->container ->get('security.context')->setToken($this->container->get("session")->get('security_token'));
            }


        //FIREWALL
        $requestMatcher = new RequestMatcher('^/admin/');
        $accessMap = new AccessMap($requestMatcher, array('ROLE_USER'));
        $httpUtils = new HttpUtils();


        //AJOUT DES ECOUTEUR TRAITANT L'AUTHENTIFICATION
        $listeners = array(
            new AuthListener(
                $this->container ->get('security.context'),
                $authenticationManager,
                $this->providerKey,
                $this->container
            )
        );

        //GESTION DES ERREURS D'AUTHENTIFICATION
        $exceptionListener = new ExceptionListener(
            $this->container ->get('security.context'),
            // default implementation of the authentication trust resolver
            new AuthenticationTrustResolver('', ''), // $anonymousClass, $rememberMeClass
            // encapsulates the logic needed to create sub-requests, redirect the user, and match URLs.
            $httpUtils,
            ''
        );


        //AJOUT DES LISTENERS AU FIREWALL
        //ET DU FIREWALL AU KERNEL
        $map = new FirewallMap();
        $map->add($requestMatcher, $listeners, $exceptionListener);
        $firewall = new Firewall($map, $this->dispatcher);
        $this->dispatcher->addListener(KernelEvents::REQUEST, array($firewall, 'onKernelRequest'));



        //--------------------------------------------------------------


        $this->finishKernel();

        var_dump("TOKEN en session =" .  $this->container->get('session')->get('security_token'));
        var_dump("TOKEN du security context =" .  $this->container ->get('security.context')->getToken());

    }



    private function initContainer(){
        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoaderDic( $this->container , new FileLocator(ROOT_PATH.'/app/config'));
        $loader->load('services.yml');
        $loader = new YamlFileLoaderDic( $this->container , new FileLocator(ROOT_PATH.'/src/config'));
        $loader->load('services.yml');
    }



    private function initRoutes(){
        $locator = new FileLocator(array(ROOT_PATH."/src/config"));
        $loader = new YamlFileLoader($locator);
        $this->routes = $loader->load('routing.yml');
    }



    private function initKernel(){

        $this->request = Request::createFromGlobals();

        $this->matcher = new UrlMatcher($this->routes, new RequestContext());

        $this->dispatcher = new EventDispatcher();
        $resolver = new MyControllerResolver( $this->container );

        $this->kernel = new HttpKernel($this->dispatcher,$resolver);
        $this->dispatcher->addSubscriber(new RouterListener($this->matcher));
    }

    private function finishKernel(){

        $response = $this->kernel->handle($this->request);
        $response->send();

        $this->kernel->terminate($this->request, $response);
    }

    private function getInMemoryProvider(){
        $this->providerKey = "my_security_str";
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
        return new DaoAuthenticationProvider(
            $userProvider,
            $userChecker,
            $this->providerKey,
            $encoderFactory
        );
    }




}