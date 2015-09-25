<?php


namespace App\Lib\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
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
     * @var string Uniquely identifies the secured area
     */
    private $providerKey;

    private $crypt_key;


    public function __construct($container){
        $this->container = $container;
        $this->providerKey = $container->get('security.parameters')->getParameters()['providers']['keys']['provider_key'];
        $this->crypt_key = $container->get('security.parameters')->getParameters()['providers']['keys']['crypt_key'];
        $this->authenticationManager = $this->container->get('security.context')->getAuthenticationManager();

    }

    public function onKernelRequest(GetResponseEvent $event)
    {

        $request = $event->getRequest();

        //CONNECTION:

        //PAR UNE SESSION, UN COOKIE
        $token_session = $this->container->get('session')->get('security_token');
        $token_cookie = $request->cookies->get('security_token');

        if($token_session!= NULL){
            $this->container->get('security.context')->getSecurityContext()->setToken($token_session);
        }
        else if($token_cookie!= NULL){
            $token_cookie = ($this->decryptToken($token_cookie));
            $authenticatedToken = $this->authenticationManager->authenticate($token_cookie);
            $this->container->get('security.context')->getSecurityContext()->setToken($authenticatedToken);
            $this->container->get('session')->set('security_token',$authenticatedToken );

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
                    $this->providerKey
                );

                try{
                    $authenticatedToken = $this->authenticationManager->authenticate($unauth_token);
                    $this->container->get('security.context')->getSecurityContext()->setToken($authenticatedToken);

                    $this->container->get('session')->set('security_token',$authenticatedToken );

                    if($remember_me){
                        $unauth_token_crypted = $this->cryptToken($user, $pass);
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


    private function cryptToken($user,$pass){

        $unauth_token_crypted  = array(
            $user,
            $pass);
        $unauth_token_crypted = serialize($unauth_token_crypted);

        $unauth_token_crypted = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $this->crypt_key,
            $unauth_token_crypted,
            "ecb");
        $unauth_token_crypted = base64_encode($unauth_token_crypted);

        return $unauth_token_crypted;
    }


    private function decryptToken($unauth_token_crypted){

        $unauth_token_crypted = base64_decode($unauth_token_crypted);

        $unauth_token_crypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $this->crypt_key,
            $unauth_token_crypted,
            "ecb");

        $unauth_token_crypted =  unserialize($unauth_token_crypted);

        return  $unauth_token = new UsernamePasswordToken(
            $unauth_token_crypted[0],
            $unauth_token_crypted[1],
            $this->providerKey
        );
    }


    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}