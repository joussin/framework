<?php
namespace Src\Entities;
/**
 * @Entity @Table(name="products", options={"collate"="utf8_unicode_ci"})
 **/
class Product
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     **/
    protected $id;
    /**
     * @Column(type="string", length=255, nullable=true)
     **/
    protected $name;
    /**
     * @Column(type="datetime", nullable=true)
     **/
    protected $date;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }



}
