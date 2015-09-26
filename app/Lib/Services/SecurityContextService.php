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
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class SecurityContextService{

    protected $security_context;
    protected $authenticationManager;


    private $security_config;
    private $encoderFactory;
    private $doctrine;

    public function __construct($security_config,$encoderFactory,$doctrine){



        $this->security_config = $security_config->getParameters();
        $this->encoderFactory =  $encoderFactory;
        $this->doctrine = $doctrine;

        //PROVIDER
        $providerKey = $this->security_config['providers']['keys']['provider_key'];

        $userChecker = new UserChecker();

        $inMemoryUserProvider = new InMemoryUserProvider($this->security_config['providers']['in_memory']['users']);
        $inMemoryUserProvider = new DaoAuthenticationProvider(
            $inMemoryUserProvider,
            $userChecker,
            $providerKey,
            $this->encoderFactory->getEncoderFactory()
        );
        $entityProvider = new EntityProvider(new User(),$this->doctrine);
        $entityProvider = new DaoAuthenticationProvider(
            $entityProvider,
            $userChecker,
            $providerKey,
            $this->encoderFactory->getEncoderFactory()
        );

        //PROVIDER MANAGER
        $providers = array(
            $inMemoryUserProvider,
            $entityProvider
        );
        $this->authenticationManager = new AuthenticationProviderManager($providers);
        //ACCES MANAGER
        $hierarchy = $this->security_config['roles']['hierarchy'];
        $roleHierarchy = new RoleHierarchy($hierarchy);
        $roleVoter = new RoleHierarchyVoter($roleHierarchy);
        $voters = array($roleVoter);
        $accessDecisionManager = new AccessDecisionManager($voters);


        $this->security_context = new SecurityContext($this->authenticationManager,$accessDecisionManager);


    }
    /**
     * @return AuthenticationProviderManager
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * @return EncoderFactory
     */
    public function getSecurityContext()
    {

        return $this->security_context;
    }







}