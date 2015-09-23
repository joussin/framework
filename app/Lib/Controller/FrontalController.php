<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 06/09/15
 * Time: 18:36
 */

namespace App\Lib\Controller;

use App\Lib\Security\AuthenticationListener;
use App\Lib\Security\EntityProvider;
use App\Lib\Security\FirewallListener;
use Src\Entities\User;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as YamlFileLoaderDic;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class FrontalController{

/*
 * TODO: fct remember me
 * TODO: userChecker -> enabled
 * TODO: entité user: email, token, enabled etc...
 * TODO: voir les redirection apres login, logout etc...
 *
 */

    public function __construct(){

        $container = new ContainerBuilder();
        $loader = new YamlFileLoaderDic($container, new FileLocator(ROOT_PATH.'/app/config'));
        $loader->load('services.yml');
        $loader = new YamlFileLoaderDic($container, new FileLocator(ROOT_PATH.'/src/config'));
        $loader->load('services.yml');
        $container->compile();

        $routes = $container->get('router')->getRoutes();
        $request = Request::createFromGlobals();
        $matcher = new UrlMatcher($routes, new RequestContext());
        $dispatcher = new EventDispatcher();


        //LISTENERS
        $dispatcher->addSubscriber(new AuthenticationListener($container));
        $dispatcher->addSubscriber(new FirewallListener($container, $matcher));
        $dispatcher->addSubscriber(new RouterListener($matcher));



        $resolver = new MyControllerResolver($container);
        $kernel = new HttpKernel($dispatcher,$resolver);
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);



        if(DEV_MODE){
//            var_dump('TOKEN session='.$container->get('session')->get('security_token'));
//            var_dump('TOKEN security context='.$container->get('security.context')->getToken());
//            var_dump($container->getServiceIds());
         }

    }
}
