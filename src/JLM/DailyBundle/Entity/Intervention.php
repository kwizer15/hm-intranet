<?php

namespace JLM\DailyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JLM\ModelBundle\Entity\Contract;
use JLM\OfficeBundle\Entity\Bill;

/**
 * Plannification d'intervention
 * JLM\DailyBundle\Entity\InterventionPlanned
 *
 * @ORM\Table(name="shifting_interventions")
 * @ORM\Entity(repositoryClass="JLM\DailyBundle\Entity\InterventionRepository")
 */
abstract class Intervention extends Shifting
{
    /**
     * Porte (lien)
     * @var JLM\ModelBundle\Entity\Door
     * @ORM\ManyToOne(targetEntity="JLM\ModelBundle\Entity\Door", inversedBy="interventions")
     * @Assert\NotNull(message="Une intervention doit être liée à une porte")
     */
    private $door;
    
    /**
     * @var string $contactName
     * @ORM\Column(name="contact_name", type="string", nullable=true)
     * @Assert\Type(type="string")
     */
    private $contactName;
    
    /**
     * @var string $contactPhone
     * @ORM\Column(name="contact_phones", type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $contactPhones;
    
    /**
     * E-mail pour envoyer le rapport
     * @var string $contactEmail
     * @ORM\Column(name="contact_email", type="text", nullable=true)
     * @Assert\Email
     */
    private $contactEmail;
   
    /**
     * Report
     * @var string $report
     * @ORM\Column(name="report",type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $report;
    
    /**
     * Commentaires (interne à la société)
     * @var string $comments
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $comments;
    
    /**
     * Priorité
     * @var int $priority
     * 1 - Très Urgent
     * 2 - Urgent
     * ....
     *
     * @ORM\Column(name="priority", type="smallint")
     * @Assert\Choice(choices = {1,2,3,4,5,6})
     * @Assert\NotBlank(message="Pas de priorité définie")
     */
    private $priority;
    
    /**
     * Closed
     * @var DateTime
     * 
     * @ORM\Column(name="close", type="datetime", nullable=true)
     * @Assert\DateTime
     */
    private $close;
    
    /**
     * Action Bureau (facturation)
     * @var JLM\OfficeBundle\Entity\Task
     *
     * @ORM\OneToOne(targetEntity="JLM\OfficeBundle\Entity\Task")
     * 
     */
    private $officeAction;
    
    /**
     * Action Bureau (autre)
     * @var JLM\OfficeBundle\Entity\Task
     *
     * @ORM\OneToOne(targetEntity="JLM\OfficeBundle\Entity\Task")
     */
    private $otherAction;
    
    /**
     * Reste a faire
     * @var string
     *
     * @ORM\Column(name="rest",type="text",nullable=true)
     * @Assert\Type(type="string")
     */
    private $rest;
    
    /**
     * Type de contrat
     * @var string
     * @ORM\Column(name="contract",type="string",nullable=true)
     * @Assert\Type(type="string")
     * @Assert\NotNull(message="Pas de type de contrat défini")
     */
    private $contract;
    
    /**
     * Numéro de bon d'intervention
     * @var string
     * @ORM\Column(name="voucher",type="string",nullable=true)
     * @Assert\Type(type="int")
     */
    private $voucher;
    
    /**
     * Facture
     * @var JLM\OfficeBundle\Entity\Bill
     * @ORM\OneToOne(targetEntity="JLM\OfficeBundle\Entity\Bill", mappedBy="intervention")
     * @Assert\Valid
     */
    private $bill;
    
    /**
     * Doit etre facturée
     * @var bool
     * @ORM\Column(name="mustBeBilled", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    private $mustBeBilled;
    
    /**
     * Get contract
     * @return string
     */
    public function getContract()
    {
    	return $this->contract;
    }
    
    /**
     * Set contract
     * @param string|Contract|null $contract
     * @return Intervention
     */
    public function setContract($contract)
    {
    	if ($contract instanceof \JLM\ModelBundle\Entity\Contract)
    		$this->contract = $contract.'';
    	elseif ($contract === null)
    		$this->contract = 'Hors contrat';
    	else $this->contract = ''.$contract;
    	return $this;
    }
    
    /**
     * Get rest
     * @return string
     */
    public function getRest()
    {
    	return $this->rest;
    }
    
    /**
     * Set rest
     * @return string
     */
    public function setRest($rest)
    {
    	$this->rest = $rest;
    	return $this;
    }
    
    /**
     * Get voucher
     * @return string
     */
    public function getVoucher()
    {
    	return $this->voucher;
    }
    
    /**
     * Set voucher
     * @return string
     */
    public function setVoucher($voucher)
    {
    	$this->voucher = $voucher;
    	return $this;
    }
    
    /**
     * Set contactName
     *
     * @param string $contactName
     * @return InterventionPlanned
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    
        return $this;
    }

    /**
     * Get contactName
     *
     * @return string 
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set contactPhones
     *
     * @param string $contactPhones
     * @return InterventionPlanned
     */
    public function setContactPhones($contactPhones)
    {
        $this->contactPhones = $contactPhones;
    
        return $this;
    }

    /**
     * Get contactPhones
     *
     * @return string 
     */
    public function getContactPhones()
    {
        return $this->contactPhones;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return InterventionPlanned
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    
        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string 
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    
    /**
     * Set door
     *
     * @param JLM\DailyBundle\Entity\Door $door
     * @return InterventionPlanned
     */
    public function setDoor(\JLM\ModelBundle\Entity\Door $door = null)
    {
        $this->door = $door;
    
        return $this;
    }

    /**
     * Get door
     *
     * @return JLM\DailyBundle\Entity\Door 
     */
    public function getDoor()
    {
        return $this->door;
    }
    
    /**
     * Get contractType
     * 
     * @return bool
     */
    public function isCompleteContract()
    {
    	$contract = $this->getDoor()->getActualContract();
    	if ($contract === null)
    		return false;
    	return $contract->getComplete();
    }

    /**
     * Get report
     *
     * @return string
     */
    public function getReport()
    {
    	return $this->report;
    }
    
    /**
     * Set report
     *
     * @param string $report
     * @return Fixing
     */
    public function setReport($report)
    {
    	$this->report = $report;
    	return $this;
    }
    
    /**
     * Set priority
     *
     * @param integer $priority
     * @return InterventionPlanned
     */
    public function setPriority($priority)
    {
    	$this->priority = $priority;
    
    	return $this;
    }
    
    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
    	return $this->priority;
    }
    
    
    /**
     * Set Closed
     * 
     * @param bool $closed
     * @return Intervention
     * @deprecated
     */
    public function setClosed($closed = true)
    {
    	$this->setClose(new \DateTime);
       	return $this;
    }
    
    /**
     * Get closed
     * 
     * @return bool
     * @deprecated
     */
    public function getClosed()
    {
    	if ($this->getClose() === null)
    		return false;
    	return true;
    }
    
    /**
     * Set Close
     *
     * @param DateTime $close
     * @return Intervention
     */
    public function setClose(\DateTime $close = null)
    {
    	if ($close === null)
    		$close = new \DateTime;
    	$this->close = $close;
    	return $this;
    }
    
    /**
     * Get close
     *
     * @return DateTime
     */
    public function getClose()
    {
    	return $this->close;
    }
    
    /**
     * Re-open
     * 
     * @return Intervention
     */
    public function reOpen()
    {
    	$this->close = null;
    	return $this;
    }
    
    /**
     * Set comments
     *
     * @param string $comments
     * @return InterventionReport
     */
    public function setComments($comments)
    {
    	$this->comments = $comments;
    
    	return $this;
    }
    
    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
    	return $this->comments;
    }
    
    /**
     * Get Office Action
     *
     * @return JLM\OfficeBundle\Entity\Task
     */
    public function getOfficeAction()
    {
    	return $this->officeAction;
    }
    
    /**
     * Set OfficeAction
     *
     * @param JLM\OfficeBundle\Entity\Task $officeAction
     * @return Fixing
     */
    public function setOfficeAction(\JLM\OfficeBundle\Entity\Task $task = null)
    {
    	$this->officeAction = $task;
    	return $this;
    }
    
    /**
     * Get Office Action
     *
     * @return JLM\OfficeBundle\Entity\Task
     */
    public function getOtherAction()
    {
    	return $this->otherAction;
    }
    
    /**
     * Set OfficeAction
     *
     * @param JLM\OfficeBundle\Entity\Task $otherAction
     * @return Fixing
     */
    public function setOtherAction(\JLM\OfficeBundle\Entity\Task $task = null)
    {
    	$this->otherAction = $task;
    	return $this;
    }
    
    /**
     * hasTachnician
     * @return boolean
     */
    public function hasTechnician()
    {
    	return (sizeof($this->getShiftTechnicians()) > 0);
    }
    
    /**
     * Get State
     * 
     * @return integer
     */
    public function getState()
    {
    
    	if (!$this->hasTechnician() && !$this->getClosed())
    		return 0;
    	if (!$this->hasTechnician() && $this->getClosed())
    		return -1;
    	if (!$this->getClosed())
    		return 1;
    	if ($this->getMustBeBilled() === null || (!$this->getOtherAction() && $this->getRest()))
    		return 2;
    	return 3;
    }
    
    /**
     * Vérifie qu'une intervention devant être facturée possède une facture
     * et inversement
     */
    public function isBilled()
    {
    	return !$this->mustBeBilled === ($this->bill === null);
    }

    /**
     * Set mustBeBilled
     *
     * @param boolean $mustBeBilled
     * @return Intervention
     */
    public function setMustBeBilled($mustBeBilled)
    {
        $this->mustBeBilled = $mustBeBilled;
    
        return $this;
    }

    /**
     * Get mustBeBilled
     *
     * @return boolean 
     */
    public function getMustBeBilled()
    {
        return $this->mustBeBilled;
    }

    /**
     * Set bill
     *
     * @param \JLM\OfficeBundle\Entity\Bill $bill
     * @return Intervention
     */
    public function setBill(\JLM\OfficeBundle\Entity\Bill $bill = null)
    {
        $this->bill = $bill;
    	
        return $this;
    }

    /**
     * Get bill
     *
     * @return \JLM\OfficeBundle\Entity\Bill 
     */
    public function getBill()
    {
        return $this->bill;
    }
}