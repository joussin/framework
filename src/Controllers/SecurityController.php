<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use App\Lib\SomeAuthenticationListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\FirewallMap;


final class SecurityController extends AbstractController
{


    public  function indexAction(Request $request){




//        $map = new FirewallMap();

//        $requestMatcher = new RequestMatcher('^/secured-area/');
//        $listener = new SomeAuthenticationListener();
//        $listeners = array($listener);

//        $exceptionListener = new \Symfony\Component\Security\Http\Firewall\ExceptionListener($this);

//        $map->add($requestMatcher, $listeners, $exceptionListener);
//
//// the EventDispatcher used by the HttpKernel
//        $dispatcher = new EventDispatcher();

//        $firewall = new Firewall($map, $dispatcher);

//        $dispatcher->addListener(KernelEvents::REQUEST, array($firewall, 'onKernelRequest'));
//
//
//
        return  $this->render("Security/index.html.twig");
    }
}