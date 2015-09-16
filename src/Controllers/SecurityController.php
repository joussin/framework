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


    public  function loginAction(Request $request){






        return  $this->render("Security/login.html.twig");
    }
}