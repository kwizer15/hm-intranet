<?php

namespace JLM\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use JLM\ContactBundle\Model\AddressInterface;

/**
 * JLM\ModelBundle\Entity\Site
 *
 * @ORM\Table(name="sites")
 * @ORM\Entity(repositoryClass="JLM\ModelBundle\Entity\SiteRepository")
 */
class Site
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
     * @var boolean $accession
     * true = accession
     * false = social
     * null = unknown
     * @ORM\Column(name="accession", type="boolean")
     * @Assert\Choice(choices={0,1})
     * @Assert\NotNull
     */
    private $accession;
    
    /**
     * @var Address $address
     * 
     * @ORM\OneToOne(targetEntity="JLM\ContactBundle\Model\AddressInterface")
     * @Assert\Valid
     * @Assert\NotNull
     */
    private $address;
    
    /**
     * @var string $groupnumber
     * 
     * @ORM\Column(name="groupNumber", type="string", length=6, nullable=true)
     * @Assert\Length(min=4,max=6)
     */
    private $groupNumber;
    
    /**
     * @var ArrayCollection $doors
     * 
     * @ORM\OneToMany(targetEntity="Door", mappedBy="site")
     * 
     */
    private $doors;
    
    /**
     * @var ArrayCollection $contacts
     *
     * @ORM\OneToMany(targetEntity="SiteContact", mappedBy="site")
     * 
     */
    private $contacts;
    
    /**
     * @var Trustee $trustee
     * 
     * @ORM\ManyToOne(targetEntity="Trustee",inversedBy="sites")
     * @Assert\Valid
     * @Assert\NotNull
     */
    private $trustee;
    
    /**
     * @var VAT $vat
     * 
     * @ORM\ManyToOne(targetEntity="VAT")
     * @Assert\Valid
     * @Assert\NotNull
     */
    private $vat;
    
    /**
     * @var Address $lodge
     *
     * @ORM\OneToOne(targetEntity="JLM\ContactBundle\Model\AddressInterface")
     * @Assert\Valid
     */
    private $lodge;
    
    /**
     * @var string $observations
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $observations;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="JLM\TransmitterBundle\Entity\UserGroup", mappedBy="site")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $userGroups;
    
    /**
     * @var ArrayColection
     * 
     * @ORM\OneToMany(targetEntity="JLM\OfficeBundle\Entity\Bill", mappedBy="siteObject")
     */
    private $bills;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->doors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bills = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set accession
     *
     * @param boolean $accession
     * @return Trustee
     */
    public function setAccession($accession)
    {
    	$this->accession = $accession;
    
    	return $this;
    }
    
    /**
     * Get accession
     *
     * @return boolean
     */
    public function getAccession()
    {
    	return $this->accession;
    }
    
    
    /**
     * Set address
     *
     * @param JLM\ContactBundle\Model\AddressInterface $address
     * @return Site
     */
    public function setAddress(AddressInterface $address = null)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return JLM\ContactBundle\Model\AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set lodge
     *
     * @param JLM\ContactBundle\Model\AddressInterface $lodge
     * @return Site
     */
    public function setLodge(AddressInterface $lodge = null)
    {
    	$this->lodge = $lodge;
    
    	return $this;
    }
    
    /**
     * Get lodge
     *
     * @return JLM\ContactBundle\Model\AddressInterface
     */
    public function getLodge()
    {
    	return $this->lodge;
    }
    
    /**
     * Set observations
     *
     * @param string $observations
     * @return Door
     */
    public function setObservations($observations)
    {
    	$this->observations = $observations;
    
    	return $this;
    }
    
    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
    	return $this->observations;
    }
    
    /**
     * Set groupnumber
     * 
     */
    public function setGroupNumber($groupNumber)
    {
    	$this->groupNumber = $groupNumber;
    	return $this;
    }
    
    /**
     * Get groupnumber
     *
     */
    public function getGroupNumber()
    {
    	return $this->groupNumber;
    }
    
    /**
     * Add doors
     *
     * @param JLM\ModelBundle\Entity\Door $doors
     * @return Site
     */
    public function addDoor(\JLM\ModelBundle\Entity\Door $doors)
    {
        $this->doors[] = $doors;
    
        return $this;
    }

    /**
     * Remove doors
     *
     * @param JLM\ModelBundle\Entity\Door $doors
     */
    public function removeDoor(\JLM\ModelBundle\Entity\Door $doors)
    {
        $this->doors->removeElement($doors);
    }

    /**
     * Get doors
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDoors()
    {
        return $this->doors;
    }

    /**
     * Add contacts
     *
     * @param JLM\ModelBundle\Entity\Person $contacts
     * @return Site
     */
    public function addContact(\JLM\ModelBundle\Entity\Person $contacts)
    {
        $this->contacts[] = $contacts;
    
        return $this;
    }

    /**
     * Remove contacts
     *
     * @param JLM\ModelBundle\Entity\Person $contacts
     */
    public function removeContact(\JLM\ModelBundle\Entity\Person $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set trustee
     *
     * @param JLM\ModelBundle\Entity\Trustee $trustee
     * @return Site
     */
    public function setTrustee(\JLM\ModelBundle\Entity\Trustee $trustee = null)
    {
        $this->trustee = $trustee;
    
        return $this;
    }

    /**
     * Get trustee
     *
     * @return JLM\ModelBundle\Entity\Trustee 
     */
    public function getTrustee()
    {
        return $this->trustee;
    }

    /**
     * Set vat
     *
     * @param JLM\ModelBundle\Entity\VAT $vat
     * @return Site
     */
    public function setVat(\JLM\ModelBundle\Entity\VAT $vat = null)
    {
        $this->vat = $vat;
    
        return $this;
    }

    /**
     * Get vat
     *
     * @return JLM\ModelBundle\Entity\VAT 
     */
    public function getVat()
    {
        return $this->vat;
    }
    
    /**
     * To String
     */
    public function __toString()
    {
    	return $this->getAddress().'';
    }
    
    /**
     * To String
     */
    public function toString()
    {
    	return $this->getAddress()->toString();
    }
    
    /**
     * Get Billing Prelabel
     * @return string
     */
    public function getBillingPrelabel()
    {
    	$prelabel = '';
    	foreach ($this->getDoors() as $door)
    	{
    		if (!$prelabel)
    		{
    			return $door->getBillingPrelabel();
    		}
    	}
    	return '';
    }

    /**
     * Add userGroups
     *
     * @param \JLM\TransmitterBundle\Entity\UserGroup $userGroups
     * @return Site
     */
    public function addUserGroup(\JLM\TransmitterBundle\Entity\UserGroup $userGroups)
    {
        $this->userGroups[] = $userGroups;
    
        return $this;
    }

    /**
     * Remove userGroups
     *
     * @param \JLM\TransmitterBundle\Entity\UserGroup $userGroups
     */
    public function removeUserGroup(\JLM\TransmitterBundle\Entity\UserGroup $userGroups)
    {
        $this->userGroups->removeElement($userGroups);
    }

    /**
     * Get userGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * Add bills
     *
     * @param \JLM\OfficeBundle\Entity\Bill $bills
     * @return Site
     */
    public function addBill(\JLM\OfficeBundle\Entity\Bill $bills)
    {
        $this->bills[] = $bills;
    
        return $this;
    }

    /**
     * Remove bills
     *
     * @param \JLM\OfficeBundle\Entity\Bill $bills
     */
    public function removeBill(\JLM\OfficeBundle\Entity\Bill $bills)
    {
        $this->bills->removeElement($bills);
    }

    /**
     * Get bills
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBills()
    {
        return $this->bills;
    }
    
    /**
     * Blocage des nouvelles intervs pour cause de retard de paiement
     * 
     * @return bool
     */
    public function isBlocked()
    {
    	$bills = $this->getBills();
    	foreach ($bills as $bill)
    	{
    		if ($bill->getState() == 1 && $bill->getSecondBoost() !== null)
    			return true;
    	}
    	return false;
    }
}