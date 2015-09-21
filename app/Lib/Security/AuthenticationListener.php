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



    public function __construct($providerKey,$anonymousKey,$authenticationManager,$roleVoter,$container){

        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->anonymousKey = $anonymousKey;
        $this->roleVoter = $roleVoter;
        $this->container = $container;

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $token = $this->container->get('session')->get('security_token');
        if($token!= NULL){
            $this->container->get('security.context')->setToken($token);

        }else{
            $token = new AnonymousToken( $this->anonymousKey, 'anonymous', array());
            $token = $this->authenticationManager->authenticate($token);
            $this->container->get('security.context')->setToken($token);

            if(
                NULL !== $request->request->get('_username') &&
                NULL !== $request->request->get('_password')
            ) {
                $user = $request->request->get('_username');
                $pass = $request->request->get('_password');

                $unAuthToken = new UsernamePasswordToken(
                    $user,
                    $pass,
                    $this->providerKey
                );

                try{
                    $authenticatedToken = $this->authenticationManager->authenticate($unAuthToken);
//            $this->roleVoter->vote($authenticatedToken, new \stdClass(), array('ROLE_ADMIN'));
                    $this->container->get('security.context')->setToken($authenticatedToken);
                    $this->container->get('session')->set('security_token',$authenticatedToken );

                }
                catch (AuthenticationException $failed) {
                    $this->container->get('session')->set('security_login_error',$failed->getMessage() );
                }
            }
        }



    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest'
        );
    }
}