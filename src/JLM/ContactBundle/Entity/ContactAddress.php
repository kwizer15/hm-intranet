<?php

namespace JLM\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JLM\ContactBundle\Model\ContactAddressInterface;
use JLM\ContactBundle\Model\AddressInterface;

/**
 * ContactAddress
 *
 * @ORM\Table(name="contact_addresses")
 * @ORM\Entity
 */
class ContactAddress extends ContactData implements ContactAddressInterface
{
	/**
	 * @var Address
	 * 
	 * @ORM\ManyToOne(targetEntity="AddressInterface")
	 */
	private $address;
    
    /**
     * @var main
     *
     * @ORM\Column(name="main", type="boolean")
     */
    private $main = false;

    /**
     * @var label
     * 
     * @ORM\Column(name="label", type="string", nullable=true)
     */
    private $label;
    
    /**
     * Constructor
     */
    public function __construct(AddressInterface $address)
    {
    	$this->setAddress($address);
    }

    /**
     * Set main
     *
     * @param boolean $main
     * @return self
     */
    public function setMain($main = true)
    {
        $this->main = (bool)$main;
    
        return $this;
    }

    /**
     * Get main
     *
     * @return boolean 
     */
    public function getMain()
    {
        return $this->main;
    }
    
    /**
     * Is main
     *
     * @return boolean
     */
    public function isMain()
    {
    	return $this->getMain();
    }
    
    /**
     * To string
     * 
     * @return string
     */
    public function __toString()
    {
    	return $this->getAddress()->__toString();
    }
    
    /**
     * get Address
     * 
     * @return Address
     */
    public function getAddress()
    {
    	return $this->address;
    }
    
    /**
     * set Address
     * 
     * @param Address $address
     * @return self
     */
    public function setAddress(AddressInterface $address)
    {
    	$this->address = $address;
    	return $this;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return ContactAddress
     */
    public function setLabel($label)
    {
    	if (empty($label))
    		$label = null;
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string|null 
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStreet()
    {
    	return $this->getAddress()->getStreet();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
    	return $this->getAddress()->getCity();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getZip()
    {
    	return $this->getAddress()->getZip();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
    	return $this->getAddress()->getCountry();
    }
}