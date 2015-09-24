<?php
namespace Src\Entities;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Entity @Table(
 * name="user",
 * options={"collate"="utf8_unicode_ci"})
 **/
class User implements AdvancedUserInterface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     **/
    protected $id;
    /**
     * @Column(type="string", length=255, nullable=false, unique = true)
     **/
    protected $username;
    /**
     * @Column(type="string", length=255, nullable=false, unique = true)
     **/
    protected $email;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $salt;

    /**
     * @Column(type="array", length=255, nullable=false)
     */
    protected $roles;
    /**
     * @Column( type="boolean", options={"default":0}, nullable=false)
     */
    protected $enabled;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $token_validation;


    public function __construct()
    {
        $this->setRoles(array('ROLE_USER'));
        $this->setEnabled(0);


    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }



    /**
     * Returns the roles granted to the user.
     *
     * @return Role[] The user roles
     */
    public function getRoles(){
        return $this->roles ;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Returns the salt.
     *
     * @return string The salt
     */
    public function getSalt(){
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getTokenValidation()
    {
        return $this->token_validation;
    }

    /**
     * @param mixed $token_validation
     */
    public function setTokenValidation($token_validation)
    {
        $this->token_validation = $token_validation;
    }





    public function isEnabled(){

        return $this->getEnabled();
    }

    public function isAccountNonLocked(){

        return true;
    }


    public function isAccountNonExpired(){

        return true;
    }
    public function isCredentialsNonExpired(){

        return true;
    }

    public function eraseCredentials(){
        $this->password = null;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return Boolean
     */
    public function equals(UserInterface $user){
        return ($this->getUsername() === $user->getUsername());
    }


}
