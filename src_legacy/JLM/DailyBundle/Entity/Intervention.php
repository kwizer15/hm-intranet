<?php

/*
 * This file is part of the JLMDailyBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\DailyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JLM\ContractBundle\Model\ContractInterface;
use JLM\OfficeBundle\Entity\AskQuote;
use JLM\CommerceBundle\Model\BillInterface;
use JLM\CommerceBundle\Model\BillSourceInterface;
use JLM\DailyBundle\Model\InterventionInterface;
use JLM\ModelBundle\Entity\Door;
use JLM\ContactBundle\Entity\Company;

/**
 * Plannification d'intervention
 * JLM\DailyBundle\Entity\InterventionPlanned
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
abstract class Intervention extends Shifting implements InterventionInterface, BillSourceInterface
{
    /**
     * Porte (lien)
     * @var JLM\ModelBundle\Entity\Door
     * @Assert\NotNull(message="Une intervention doit être liée à une porte")
     */
    private $door;
    
    /**
     * @var string $contactName
     * @Assert\Type(type="string")
     */
    private $contactName;
    
    /**
     * @var string $contactPhone
     * @Assert\Type(type="string")
     */
    private $contactPhones;
    
    /**
     * E-mail pour envoyer le rapport
     * @var string $contactEmail
     * @Assert\Email
     */
    private $contactEmail;
   
    /**
     * Report
     * @var string $report
     * @Assert\Type(type="string")
     */
    private $report;
    
    /**
     * Commentaires (interne à la société)
     * @var string $comments
     * @Assert\Type(type="string")
     */
    private $comments;
    
    /**
     * Priorité
     * @var int $priority
     * 1 - Très Urgent
     * 2 - Urgent
     * ....
     * @Assert\Choice(choices = {1,2,3,4,5,6})
     * @Assert\NotBlank(message="Pas de priorité définie")
     */
    private $priority;
    
    /**
     * Closed
     * @var DateTime
     * @Assert\DateTime
     */
    private $close;
    
    /**
     * Reste a faire
     * @var string
     * @Assert\Type(type="string")
     */
    private $rest;
    
    /**
     * Type de contrat
     * @var string
     * @Assert\Type(type="string")
     * Assert\NotNull(message="Pas de type de contrat défini")
     */
    private $contract;
    
    /**
     * Numéro de bon d'intervention
     * @var string
     */
    private $voucher;
    
    /**
     * Facture
     * @var BillInterface
     * @Assert\Valid
     */
    private $bill;
    
    /**
     * Facture externe
     * @var string
     */
    private $externalBill;
    
    /**
     * Doit etre facturée
     * @var bool
     * @Assert\Type(type="bool")
     */
    private $mustBeBilled;
    
    /**
     * Intervention lancée pour le reste à faire
     * @var Work
     */
    private $work;
    
    /**
     * Demande de devis lancée pour le reste à faire
     * @var AskQuote
     */
    private $askQuote;
    
    /**
     * Contacter client pour le reste à faire
     * @var bool
     */
    private $contactCustomer = null;
    
    /**
     * Annuler l'intervention
     * @var string
     */
    private $cancel = false;
    
    /**
     * Publication
     * @var bool
     */
    private $published = false;
    
    /**
     * Get contract
     * @return string
     */
    public function getContract()
    {
        return $this->contract;
    }
    
    /**
     * Get dynamic contract
     * @return ContractInterface
     */
    public function getDynContract()
    {
        $techs = $this->getShiftTechnicians();
        if (sizeof($techs) == 0) {
            return $this->getDoor()->getContract();
        }
        $firstDate = new \DateTime;
        foreach ($techs as $tech) {
            $firstDate = ($tech->getBegin() < $firstDate) ? $tech->getBegin() : $firstDate;
        }
        
        return $this->getDoor()->getContract($firstDate);
    }
    
    /**
     * Set contract
     * @param string|ContractInterface|null $contract
     * @return Intervention
     */
    public function setContract($contract)
    {
        if ($contract instanceof ContractInterface) {
            $this->contract = $contract.'';
        } elseif ($contract === null) {
            $this->contract = 'Hors contrat';
        } else {
            $this->contract = ''.$contract;
        }
        
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
    public function setRest($rest = null)
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
    public function setDoor(Door $door = null)
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

        return ($contract === null) ? false : $contract->getComplete();
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
    public function setReport($report = null)
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
        return ($this->getClose() !== null);
    }
    
    /**
     * Set Close
     *
     * @param DateTime $close
     * @return Intervention
     */
    public function setClose(\DateTime $close = null)
    {
        $this->close = ($close === null) ? new \DateTime : $close;

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
    
        if (!$this->hasTechnician() && !$this->getClosed()) {
            return 0;
        }
        if (!$this->hasTechnician() && $this->getClosed() && !$this->getCancel()) {
            return -1;
        }
        if (!$this->getClosed()) {
            return 1;
        }
        if ($this->getMustBeBilled() === null
            || ($this->getContactCustomer() === null
                && $this->getAskQuote() === null
                && $this->getWork() === null
                && $this->getRest())
        ) {
            return 2;
        }
        
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
    public function setMustBeBilled($mustBeBilled = null)
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
     * @param Bill $bill
     * @return Intervention
     */
    public function setBill(BillInterface $bill = null)
    {
        $this->bill = $bill;
        
        return $this;
    }

    /**
     * Get bill
     *
     * @return Bill
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * Set externalBill
     *
     * @param string $number
     * @return Intervention
     */
    public function setExternalBill($number = null)
    {
        $this->externalBill = $number;
         
        return $this;
    }
    
    /**
     * Get externalBill
     *
     * @return string
     */
    public function getExternalBill()
    {
        return $this->externalBill;
    }
    
    /**
     * Set contactCustomer
     *
     * @param boolean $contactCustomer
     * @return Intervention
     */
    public function setContactCustomer($contactCustomer = null)
    {
        $this->contactCustomer = $contactCustomer;
    
        return $this;
    }

    /**
     * Get contactCustomer
     *
     * @return boolean
     */
    public function getContactCustomer()
    {
        return $this->contactCustomer;
    }

    /**
     * Set work
     *
     * @param \JLM\DailyBundle\Entity\Work $work
     * @return Intervention
     */
    public function setWork(Work $work = null)
    {
        $this->work = $work;
    
        return $this;
    }

    /**
     * Get work
     *
     * @return \JLM\DailyBundle\Entity\Work
     */
    public function getWork()
    {
        return $this->work;
    }
    
    /**
     * Has work
     *
     * @return bool
     */
    public function hasWork()
    {
        return $this->work !== null;
    }

    /**
     * Set askQuote
     *
     * @param \JLM\OfficeBundle\Entity\AskQuote $askQuote
     * @return Intervention
     */
    public function setAskQuote(AskQuote $askQuote = null)
    {
        $this->askQuote = $askQuote;
    
        return $this;
    }

    /**
     * Get askQuote
     *
     * @return \JLM\OfficeBundle\Entity\AskQuote
     */
    public function getAskQuote()
    {
        return $this->askQuote;
    }
    
    /**
     * Has ask quote
     *
     * @return bool
     */
    public function hasAskQuote()
    {
        return $this->askQuote !== null;
    }
    
    /**
     * Test pour les actions suite à reste à faire
     * @Assert\IsTrue(message="Une action reste à faire ne peut pas exister si le champ reste à faire est vide")
     */
    public function isRestActionValid()
    {
        return !(!$this->getRest() && (
            $this->getAskQuote() !== null
            || $this->getWork() !== null
            || $this->getContactCustomer())
        );
    }
    
    /**
     * Annuler une intervention
     */
    public function setCancel($cancel = false)
    {
        $this->cancel = (bool)$cancel;
        
        return $this;
    }
    
    /**
     * Raison de l'annulation
     */
    public function getCancel()
    {
        return $this->cancel;
    }
    
    /**
     * Intervention annulée ?
     */
    public function isCanceled()
    {
        return $this->getCancel();
    }
    
    /**
     * Get place
     * @return string
     */
    public function getPlace()
    {
        return (null === $door = $this->getDoor())
            ? parent::getPlace()
            : $door->getType().' - '.$door->getLocation().chr(10).$door->getAddress()->toString()
        ;
    }
    
    /**
     * Annuler l'intervention
     */
    public function cancel()
    {
        if ($this->getReport() !== null) {
            $this->setCancel(true);
            $this->setMustBeBilled(false);
            $this->setRest();
            $this->setClosed(new \DateTime);
        }

        return $this;
    }
    
    /**
     * Désannule l'intervention
     */
    public function uncancel()
    {
        $this->setReport();
        $this->setCancel(false);
        $this->setMustBeBilled();
        $this->reOpen();
        return $this;
    }
    
    public function getAdministratorContacts()
    {
        return $this->createContactFromEmail($this->getDoor()->getAdministratorEmails());
    }
    
    public function getManagerContacts()
    {
        return $this->createContactFromEmail($this->getDoor()->getManagerEmails());
    }
    
    private function createContactFromEmail($emails)
    {
        $c = [];
        if ($emails === null) {
            return $c;
        }
    
        foreach ($emails as $email) {
            $temp = new Company();
            $temp->setEmail($email);
            $c[] = $temp;
        }
    
        return $c;
    }
    
    public function getInstallationCode()
    {
        if ($this->getDoor() === null) {
            return null;
        }
    
        return $this->getDoor()->getCode();
    }
    
    /**
     * Is published
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }
    
    /**
     * Set published
     * @param boolean $published
     * @return self
     */
    public function setPublished($published)
    {
        $this->published = (bool)$published;
        
        return $this;
    }
    
    abstract public function getCustomerDesignation();
}
