<?php
namespace JLM\DailyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plannification de travaux
 * JLM\DailyBundle\Entity\Work
 *
 * @ORM\Table(name="shifting_works")
 * @ORM\Entity
 */
class Work extends Intervention
{
	/**
	 * Devis source
	 * 
	 * @ORM\OneToOne(targetEntity="JLM\OfficeBundle\Entity\Quote")
	 */
	private $quote;
	
	/**
	 * Work category
	 * 
	 * @ORM\ManyToOne(targetEntity="WorkCategory")
	 */
	private $category;
	
	/**
	 * Work objective
	 *
	 * @ORM\ManyToOne(targetEntity="WorkObjective")
	 */
	private $objective;
	
	/**
	 * Get Quote
	 * @return Quote
	 */
	public function getQuote()
	{
		return $this->quote;
	}
	
	/**
	 * Set Quote
	 * 
	 * @param Quote $quote
	 * @return Work
	 */
	public function setQuote(\JLM\OfficeBundle\Entity\Quote $quote = null)
	{
		$this->quote = $quote;
		return $this;
	}
	
	/**
	 * Get work category
	 * @return WorkCategory
	 */
	public function getCategory()
	{
		return $this->category;
	}
	
	/**
	 * Set work category
	 *
	 * @param WorkCategory $category
	 * @return Work
	 */
	public function setCategory(WorkCategory $category = null)
	{
		$this->category = $category;
		return $this;
	}
	
	/**
	 * Get work objective
	 * @return WorkObjective
	 */
	public function getObjective()
	{
		return $this->objective;
	}
	
	/**
	 * Set work objective
	 *
	 * @param WorkObjective $objective
	 * @return Work
	 */
	public function setObjective(WorkObjective $objective = null)
	{
		$this->objective = $objective;
		return $this;
	}
}