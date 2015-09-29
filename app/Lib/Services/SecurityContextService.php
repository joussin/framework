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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;

class SecurityContextService extends SecurityContext{

    private $security_config;
    private $encoderFactory;
    private $doctrine;

    /**
     * @param $security_config
     * @param $encoderFactory
     * @param $doctrine
     */
    public function __construct($security_config,$encoderFactory,$doctrine){
        $this->security_config = $security_config;
        $this->encoderFactory =  $encoderFactory;
        $this->doctrine = $doctrine;
        parent::__construct($this->getAuthenticationManager(),$this->getAccessDecisionManager());
    }

    /**
     * @return AccessDecisionManager
     */
    public function getAccessDecisionManager(){

        $hierarchy = $this->security_config['roles']['hierarchy'];
        $roleHierarchy = new RoleHierarchy($hierarchy);
        $roleVoter = new RoleHierarchyVoter($roleHierarchy);
        $voters = array($roleVoter);
        return new AccessDecisionManager($voters);
    }

    /**
     * @return array
     */
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
     * @param $user
     * @param $pass
     * @return array|string
     */
    public function cryptToken($user,$pass){

        $crypt_key = $this->security_config['providers']['keys']['crypt_key'];

        $unauth_token_crypted  = array(
            $user,
            $pass);
        $unauth_token_crypted = serialize($unauth_token_crypted);

        $unauth_token_crypted = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $crypt_key,
            $unauth_token_crypted,
            "ecb");
        $unauth_token_crypted = base64_encode($unauth_token_crypted);

        return $unauth_token_crypted;
    }

    /**
     * @param $unauth_token_crypted
     * @return UsernamePasswordToken
     */
    public function decryptToken($unauth_token_crypted){

        $crypt_key = $this->security_config['providers']['keys']['crypt_key'];

        $unauth_token_crypted = base64_decode($unauth_token_crypted);

        $unauth_token_crypted = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $crypt_key,
            $unauth_token_crypted,
            "ecb");

        $unauth_token_crypted =  unserialize($unauth_token_crypted);

        return  $unauth_token = new UsernamePasswordToken(
            $unauth_token_crypted[0],
            $unauth_token_crypted[1],
            $this->security_config['providers']['keys']['provider_key']
        );
    }
}