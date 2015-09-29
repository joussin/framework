<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;


use App\Lib\Security\EntityProvider;
use Src\Entities\User;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class SecurityService{

    private $security_config;
    private $encoderFactory;
    private $doctrine;

    public function __construct($security_config,$encoderFactory,$doctrine){
        $this->security_config = $security_config;
        $this->encoderFactory =  $encoderFactory;
        $this->doctrine = $doctrine;
    }

    public function getAccessDecisionManager(){
        //ACCES MANAGER
        $hierarchy = $this->security_config['roles']['hierarchy'];
        $roleHierarchy = new RoleHierarchy($hierarchy);
        $roleVoter = new RoleHierarchyVoter($roleHierarchy);
        $voters = array($roleVoter);
        return new AccessDecisionManager($voters);
    }

    public function getProviders(){

        $providerKey = $this->security_config['providers']['keys']['provider_key'];

        $userChecker = new UserChecker();

        $inMemoryUserProvider = new InMemoryUserProvider($this->security_config['providers']['in_memory']['users']);
        $inMemoryUserProvider = new DaoAuthenticationProvider(
            $inMemoryUserProvider,
            $userChecker,
            $providerKey,
            $this->encoderFactory
        );
        $entityProvider = new EntityProvider(new User(),$this->doctrine);
        $entityProvider = new DaoAuthenticationProvider(
            $entityProvider,
            $userChecker,
            $providerKey,
            $this->encoderFactory
        );

        return array(
            $inMemoryUserProvider,
            $entityProvider
        );
    }

    /**
     * @return AuthenticationProviderManager
     */
    public function getAuthenticationManager()
    {
        $providers = $this->getProviders();
        return new AuthenticationProviderManager($providers);
    }

    /**
     * @return EncoderFactory
     */
    public function getSecurityContext()
    {
        $accessDecisionManager = $this->getAccessDecisionManager();
        $authenticationManager = $this->getAuthenticationManager();
        return new SecurityContext($authenticationManager,$accessDecisionManager);
    }
}