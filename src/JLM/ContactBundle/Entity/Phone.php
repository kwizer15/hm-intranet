<?php

namespace JLM\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JLM\ContactBundle\Entity\PhoneRule;

class PhoneException extends \Exception {};

/**
 * Phone
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Phone
{
    /**
     * @var PhoneRule
     *
     * @ORM\ManyToOne(targetEntity="PhoneRule")
     */
    private $rule;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=20)
     */
    private $number = null;
    
    /**
     * Construct
     */
    public function __construct(PhoneRuleInterface $rule)
    {
    	$this->rule = $rule;
    }
    
    /**
     * Set rule
     *
     * @param PhoneRule $rule
     * @return Phone
     */
    public function setRule(PhoneRule $rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * Get rule
     *
     * @return \stdClass 
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return self
     */
    public function setNumber($number)
    {
    	$number = strtoupper(trim($number));
    	if (!$this->getRule()->isValid($number))
    		throw new PhoneException('Number format not compatible with PhoneRule');
    	// Mise au format
    	$number = str_replace(array(' ',',','.','-','/'),'',$number);
    	$format = $this->getRule()->getFormat();
    	$j = strlen($number) - 1;
    	$formatted = '';
    	for ($i = strlen($format) - 1 ; $i > 0 ; $i--)
    	{
    		switch($format[$i])
    		{
    			case 'N':
    			case 'L':
    				$formatted = $number[$j].$formatted;
    			case 'I':
    				$j--;
    				break;
    		}
    		
    	}
        $this->number = $formatted;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber($local = true)
    {
    	if ($this->number === null) return null;
    	$format = $this->getRule()->getFormat();
    	$j = 0;
    	$out = '';
    	for ($i = 0; $i < strlen($format); $i++)
    	{
    		switch ($format[$i])
    		{
    			case 'I':
    				$out .= (!$local) ? '+'.$this->getRule()->getCode() : $this->getRule()->getLocalCode();
    				break;
    			case 'N':
    			case 'L':
    				$out .= $this->number[$j++];
    				break;
    			default:
    				$out .= $format[$i];
    		}
    	}
    	
        return $out;
    }
    
    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->getNumber();
    }
}
