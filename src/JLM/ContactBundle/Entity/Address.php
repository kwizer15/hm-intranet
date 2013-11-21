<?php

namespace JLM\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JLM\ContactBundle\Entity\City;

/**
 * JLM\ContactBundle\Entity\Address
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity
 */
class Address
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
     * @var string $street
     *
     * @ORM\Column(name="street", type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $street = '';
    
    /**
     * @var string $city
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @Assert\NotNull
     * @Assert\Valid
     */
    private $city;
    
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
     * Set street
     *
     * @param string $street
     * @return self
     */
    public function setStreet($street)
    {
        $this->street = (string)$street;
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return self
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * To String
     * @return string
     */
    public function __toString()
    {
    	return ($this->getStreet() == '') ? (string)$this->getCity() : $this->getStreet().chr(10).$this->getCity();
    }
}