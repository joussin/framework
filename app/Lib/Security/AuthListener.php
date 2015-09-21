<?php

namespace App\Lib\Security;

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

// ...

public function __construct($securityContext,$authenticationManager,$providerKey){
    $this->securityContext = $securityContext;
    $this->authenticationManager = $authenticationManager;
   $this->providerKey = $providerKey;
}


    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $username = "stef";
        $password = "password";

        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->providerKey
        );

        $authenticatedToken = $this
            ->authenticationManager
            ->authenticate($unauthenticatedToken);

        $this->securityContext->setToken($authenticatedToken);
    }
}