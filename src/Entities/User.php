<?php
namespace Src\Entities;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Entity @Table(
 * name="user",
 * uniqueConstraints={@UniqueConstraint(name="user_idx", columns={"username"})},
 * options={"collate"="utf8_unicode_ci"})
 **/
class User implements UserInterface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     **/
    protected $id;
    /**
     * @Column(type="string", length=255, nullable=false)
     **/
    protected $username;

    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $password;


    /**
     * @Column(type="array", length=255, nullable=false)
     */
    protected $roles;



    public function __construct()
    {
        $this->setRoles(array('ROLE_USER'));
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
     * Returns the salt.
     *
     * @return string The salt
     */
    public function getSalt(){
        return null;
    }



    /**
     * Removes sensitive data from the user.
     *
     * @return void
     */
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
