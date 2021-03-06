<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Entity;

use JLM\ContactBundle\Model\PersonInterface;
use JLM\ContactBundle\Model\ContactInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
abstract class PersonDecorator extends ContactDecorator implements PersonInterface
{
    /**
     * Get person
     */
    public function getPerson()
    {
        return $this->getContact();
    }

    /**
     * {@inheritdoc}
     */
    public function setContact(ContactInterface $contact)
    {
        if ($contact instanceof PersonInterface) {
            return parent::setContact($contact);
        }

        return $this;
    }

    /**
     *
     * @param PersonInterface $person
     *
     * @return \JLM\ContactBundle\Entity\PersonDecorator
     */
    public function setPerson(PersonInterface $person)
    {
        return $this->setContact($person);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getPerson()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->getPerson()->getFirstName();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->getPerson()->getLastName();
    }

    /**
     * {@inheritdoc}
     */
    public function getFixedPhone()
    {
        return $this->getPerson()->getFixedPhone();
    }

    /**
     * {@inheritdoc}
     */
    public function getMobilePhone()
    {
        return $this->getPerson()->getMobilePhone();
    }
}
