<?php


namespace App\Lib\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $container;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;


    /**
     * @param $container
     */
    public function __construct($container){
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {

        $request = $event->getRequest();

        //CONNECTION:
        //PAR UNE SESSION, UN COOKIE
        $token_session = $this->container->get('session')->get('security_token');
        $token_cookie = $request->cookies->get('security_token');

        if($token_session!= NULL){
            $this->container->get('security.context')->setToken($token_session);
        }
        else if($token_cookie!= NULL){
            $token_cookie = ($this->container->get('security.context')->decryptToken($token_cookie));
            $this->authenticate($token_cookie);

        }
        //PAR LE FORMULAIRE
        else{
            if(
                NULL !== $request->request->get('_username') &&
                NULL !== $request->request->get('_password')
            ) {

                $user = $request->request->get('_username');
                $pass = $request->request->get('_password');
                $remember_me  = ($request->request->get('_remember_me')!=NULL)?true:false;

                $unauth_token = new UsernamePasswordToken(
                    $user,
                    $pass,
                    $this->container->get('security.parameters')['providers']['keys']['provider_key']
            );

                try{
                    $this->authenticate($unauth_token);

                    if($remember_me){
                        $unauth_token_crypted = $this->container->get('security.context')->cryptToken($user, $pass);
                        $cookie = new Cookie("security_token", $unauth_token_crypted, time()+ 3600 * 24 * 7);
                        $response = new Response();
                        $response->headers->setCookie($cookie);
                        $response->send();
                    }
                }
                catch (AuthenticationException $failed) {
                    $this->container->get('session')->set('security_login_error',$failed->getMessage() );
                }
            }
        }
    }

    /**
     * @param $unauth_token
     */
    private function authenticate($unauth_token){
        $this->authenticationManager = $this->container->get('security.context')->getAuthenticationManager();
        $authenticatedToken = $this->authenticationManager->authenticate($unauth_token);
        $this->container->get('security.context')->setToken($authenticatedToken);
        $this->container->get('session')->set('security_token',$authenticatedToken );

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}