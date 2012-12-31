<?php

namespace JLM\OfficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JLM\OfficeBundle\Entity\IntroModel
 *
 * @ORM\Table(name="intromodel")
 * @ORM\Entity(readOnly=true,repositoryClass="JLM\OfficeBundle\Entity\TextModelRepository")
 */
class IntroModel extends TextModel
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