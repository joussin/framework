<?php


namespace App\Lib\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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

    private $anonymousKey;


    /**
     * @var
     */
    private $roleVoter;



    public function __construct($container){

        $this->authenticationManager = $container->get('security.context')->getAuthenticationManager();
        $this->providerKey = $container->get('security.parameters')->getParameters()['providers']['keys']['provider_key'];
        $this->anonymousKey = $container->get('security.parameters')->getParameters()['providers']['keys']['anonymous_key'];
        $this->roleVoter = $container->get('security.context')->getRoleVoter();
        $this->container = $container;

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $token_session = $this->container->get('session')->get('security_token');
        if($token_session!= NULL){
            $this->container->get('security.context')->getSecurityContext()->setToken($token_session);
            return;
        }
        else if(NULL !== $request->request->get('_username') && NULL !== $request->request->get('_password')) {
                $user = $request->request->get('_username');
                $pass = $request->request->get('_password');

                $unAuthToken = new UsernamePasswordToken(
                    $user,
                    $pass,
                    $this->providerKey
                );

                try{
                    $authenticatedToken = $this->authenticationManager->authenticate($unAuthToken);
                    $this->container->get('security.context')->getSecurityContext()->setToken($authenticatedToken);
                    $this->container->get('session')->set('security_token',$authenticatedToken );
                    return;
                }
                catch (AuthenticationException $failed) {
                    $this->container->get('session')->set('security_login_error',$failed->getMessage() );
                    return;
                }
            }

        $token = new AnonymousToken( $this->anonymousKey, 'anonymous', array());
        $token = $this->authenticationManager->authenticate($token);
        $this->container->get('security.context')->getSecurityContext()->setToken($token);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}