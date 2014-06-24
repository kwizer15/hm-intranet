<?php

namespace JLM\ContactBundle\Entity;

use JLM\ContactBundle\Model\ContactAddressInterface;
use JLM\ContactBundle\Model\ContactInterface;
use JLM\ContactBundle\Model\AddressInterface;

/**
 * ContactAddress
 */
class ContactAddress extends ContactData implements ContactAddressInterface
{
	/**
	 * @var Address
	 */
	private $address;
    
    /**
     * @var main
     */
    private $main = false;

    /**
     * @var label
     */
    private $label;
    
    /**
     * Constructor
     */
    public function __construct(ContactInterface $contact, $alias, AddressInterface $address)
    {
    	$this->setContact($contact);
    	$this->setAlias($alias);
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
	 * {@inheritdoc}
	 */
    public function isMain()
    {
    	return $this->main;
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
    public function setLabel($label = null)
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
    	if ($this->label === null)
    		return $this->getContact()->getName();
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