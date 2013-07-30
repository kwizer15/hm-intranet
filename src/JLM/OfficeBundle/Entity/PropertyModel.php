<?php

namespace JLM\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JLM\OfficeBundle\Entity\PropertyModel
 *
 * @ORM\Table(name="propertymodel")
 * @ORM\Entity(readOnly=true,repositoryClass="JLM\OfficeBundle\Entity\TextModelRepository")
 */
class PropertyModel extends TextModel
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}