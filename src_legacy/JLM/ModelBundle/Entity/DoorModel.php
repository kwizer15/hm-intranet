<?php

/*
 * This file is part of the JLMModelBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ModelBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class DoorModel
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     */
    private $name = '';

    /**
     * Type de porte
     *
     * @var DoorType $type
     */
    private $type;

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
     * Set text
     *
     * @param string $text
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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

    /**
     * Set type
     *
     * @param JLM\ModelBundle\Entity\DoorType $type
     *
     * @return Door
     */
    public function setType(DoorType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return JLM\ModelBundle\Entity\DoorType
     */
    public function getType()
    {
        return $this->type;
    }
}
