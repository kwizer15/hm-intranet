<?php
namespace JLM\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JLM\ModelBundle\Entity\NameModel
 *
 * @ORM\MappedSuperclass
 */
abstract class StringModel
{
	/**
	 * @var string
	 * 
	 * @ORM\Column(name="name")
	 */
	private $name;
	
	/**
	 * Set text
	 *
	 * @param string $text
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get text
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * To String
	 */
	public function __toString()
	{
		return $this->getName();
	}
}