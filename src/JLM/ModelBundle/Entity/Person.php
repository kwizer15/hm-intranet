<?php

namespace JLM\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * JLM\ModelBundle\Entity\Person
 *
 * @ORM\Table(name="persons")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 * 		"person" = "Person",
 *      "technician" = "Technician",
 * })
 */
class Person 
{
	/**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
	/**
	 * M. Mme Mlle
	 * @var string $title
	 * 
	 * @ORM\Column(name="title", type="string", length=4)
	 */
	private $title;
	
    /**
     * @var string $firstName
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;
    
    /**
     * @var string $fixedPhone
     * 
     * @ORM\Column(name="fixedPhone",type="string", length=20, nullable=true)
     */
    private $fixedPhone;
    
    /**
     * @var string $mobilePhone
     *
     * @ORM\Column(name="mobilePhone",type="string", length=20, nullable=true)
     */
    private $mobilePhone;

    
    /**
     * @var string $fax
     *
     * @ORM\Column(name="fax",type="string", length=20, nullable=true)
     */
    private $fax;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email",type="string", length=255, nullable=true)
     */
    private $email;
    
    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
    	return trim($this->title.' '.$this->lastName.' '.$this->firstName);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set fixedPhone
     *
     * @param string $fixedPhone
     */
    public function setFixedPhone($fixedPhone)
    {
        $this->fixedPhone = $fixedPhone;
    }

    /**
     * Get fixedPhone
     *
     * @return string 
     */
    public function getFixedPhone()
    {
        return $this->fixedPhone;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * Get mobilePhone
     *
     * @return string 
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

   

    /**
     * Set fax
     *
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * To String
     * 
     * @return string
     */
    public function __toString()
    {
    	return $this->getName();
    }
}