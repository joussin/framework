<?php

namespace App\Lib\Security;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthListener implements ListenerInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var string Uniquely identifies the secured area
     */
    private $providerKey;

    private $container;

// ...

public function __construct($securityContext,$authenticationManager,$providerKey,$container){
    $this->securityContext = $securityContext;
    $this->authenticationManager = $authenticationManager;
   $this->providerKey = $providerKey;
   $this->container = $container;
}


    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if(
            NULL !== $request->request->get('_username') &&
            NULL !== $request->request->get('_password')
        ) {

            $username = $request->request->get('_username');
            $password = $request->request->get('_password');

            $username = "stef";
            $password = "password";

            $unauthenticatedToken = new UsernamePasswordToken(
                $username,
                $password,
                $this->providerKey
            );
        try{
            $authenticatedToken = $this
                ->authenticationManager
                ->authenticate($unauthenticatedToken);

            $this->securityContext->setToken($authenticatedToken);
            $this->container->get("session")->set('security_token',$authenticatedToken);
        }
        catch (AuthenticationException $failed) {
            $this->container->get('session')->set('security_login_error',$failed->getMessage() );
        }



        }

    }
}