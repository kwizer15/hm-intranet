<?php

namespace JLM\ModelBundle\Entity;

use JLM\ContractBundle\Model\ContractInterface;

/**
 * JLM\ModelBundle\Entity\Contract
 */
class Contract implements ContractInterface
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $number
     */
    private $number;

    /**
     * @var Trustee $trustee
     */
    private $trustee;
    
    /**
     * Contrat complet
     * @var bool $complete
     */
    private $complete;
    
    /**
     * Contract C1 C2...
     * @var smallint $option
     */
    private $option;
    
    /**
     * @var Door $door
     */
    private $door;
    
    /**
     * @var datetime $begin
     */
    private $begin;

    /**
     * @var datetime $endWarranty
     * @deprecated
     */
    private $endWarranty;

    /**
     * @var datetime $end
     */
    private $end;

    /**
     * @var decimal $fee
     */
    private $fee;
    
    /**
     * To String
     */
    public function __toString()
    {
//    	if ($this->getInProgress())
    		return ($this->isComplete() ? 'C'.($this->getOption()+1) : 'N'.($this->getOption()+3));
//    	return 'HC';
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
     * Set number
     *
     * @param string $number
     * @return Contract
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set complete
     *
     * @param boolean $complete
     * @return Contract
     */
    public function setComplete($complete = true)
    {
        $this->complete = $complete;
    
        return $this;
    }

    /**
     * Get complete
     *
     * @return boolean 
     */
    public function getComplete()
    {
        return $this->complete;
    }
    
    /**
     * Is complete
     *
     * @return boolean
     */
    public function isComplete()
    {
    	return $this->getComplete();
    }

    /**
     * Set normal
     *
     * @param boolean $normal
     * @return Contract
     */
    public function setNormal($normal = true)
    {
    	$this->complete = !$normal;
    
    	return $this;
    }
    
    /**
     * Get normal
     *
     * @return boolean
     */
    public function getNormal()
    {
    	return !$this->getComplete();
    }
    
    /**
     * Is normal
     *
     * @return boolean
     */
    public function isNormal()
    {
    	return $this->getNormal();
    }
    
    /**
     * Set option
     *
     * @param integer $option
     * @return Contract
     */
    public function setOption($option)
    {
        $this->option = $option;
    
        return $this;
    }

    /**
     * Get option
     *
     * @return integer 
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set begin
     *
     * @param \DateTime $begin
     * @return Contract
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;
    
        return $this;
    }

    /**
     * Get begin
     *
     * @return \DateTime 
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set endWarranty
     *
     * @deprecated
     * @param \DateTime $endWarranty
     * @return Contract
     */
    public function setEndWarranty($endWarranty)
    {
        $this->endWarranty = $endWarranty;
    
        return $this;
    }

    /**
     * Get endWarranty
     * @deprecated
     * @return \DateTime 
     */
    public function getEndWarranty()
    {
        return $this->endWarranty;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Contract
     */
    public function setEnd($end)
    {
        $this->end = $end;
    
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }
    
    /**
     * Get inProgress
     *
     * @return bool
     */
    public function getInProgress($date = null)
    {
    	$date = ($date === null) ? new \DateTime : $date;
    	return (($this->end > $date || $this->end === null) && ($this->begin <= $date));
    }

    /**
     * Set fee
     *
     * @param float $fee
     * @return self
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    
        return $this;
    }

    /**
     * Get fee
     *
     * @return float 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set door
     *
     * @param JLM\ModelBundle\Entity\Door $door
     * @return self
     */
    public function setDoor(\JLM\ModelBundle\Entity\Door $door = null)
    {
        $this->door = $door;
    
        return $this;
    }

    /**
     * Get door
     *
     * @return JLM\ModelBundle\Entity\Door 
     */
    public function getDoor()
    {
        return $this->door;
    }

    /**
     * Set trustee
     *
     * @param JLM\ModelBundle\Entity\Trustee $trustee
     * @return self
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
     * Test la date de fin
     * @return boolean
     */
    public function isEndAfterBegin()
    {
    	if ($this->end === null)
    		return true;
    	return $this->end > $this->begin;
    }
    
    /**
     * Test la date de garantie
     * @deprecated
     * @return boolean
     */
    public function isEndWarrantyAfterBegin()
    {
    	if ($this->endWarranty === null)
    		return true;
    	return $this->endWarranty > $this->begin;
    }
}