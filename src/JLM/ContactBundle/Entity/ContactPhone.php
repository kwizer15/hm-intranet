<?php

namespace JLM\ContactBundle\Entity;

use JLM\ContactBundle\Entity\ContactData;
use JLM\ContactBundle\Model\ContactInterface;
use JLM\ContactBundle\Model\ContactPhoneInterface;
use JLM\ContactBundle\Model\PhoneInterface;

/**
 * ContactPhone
 */
class ContactPhone extends ContactData implements ContactPhoneInterface
{
    /**
     * @var Phone
     */
    private $phone;
    
    /**
     * Constructor
     * @param ContactInterface $contact
     * @param string $alias
     * @param PhoneInterface $phone
     */
    public function __construct(ContactInterface $contact, $alias, PhoneInterface $phone)
    {
    	$this->setContact($contact);
    	$this->setAlias($alias);
    	$this->setPhone($phone);
    }
    
    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
    	return (string)$this->getPhone();
    }

    /**
     * Set phone
     *
     * @param \JLM\ContactBundle\Entity\Phone $phone
     * @return ContactPhone
     */
    public function setPhone(PhoneInterface $phone = null)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return \JLM\ContactBundle\Entity\Phone 
     */
    public function getPhone()
    {
        return $this->phone;
    }
}