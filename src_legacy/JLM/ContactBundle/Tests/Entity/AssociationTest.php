<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Tests\Entity;

use JLM\ContactBundle\Entity\Association;
use JLM\ContactBundle\Model\AddressInterface;
use JLM\ContactBundle\Model\AssociationInterface;
use JLM\ContactBundle\Model\ContactPhoneInterface;
use JLM\ContactBundle\Model\CorporationContactInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class AssociationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Association
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entity = new Association();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertPreConditions()
    {
        $this->assertInstanceOf(AssociationInterface::class, $this->entity);
        $this->assertNull($this->entity->getId());
        $this->assertCount(0, $this->entity->getContacts());
    }

    public function getAttributes()
    {
        return [
            ['Name','Foo',],
            [ 'Email','commerce@jlm-entreprise.fr',],
            ['Address',$this->createMock(AddressInterface::class),],
        ];
    }

    /**
     * Test getters and setters
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @dataProvider getAttributes
     */
    public function testGettersSetters($attribute, $value)
    {
        $getter = 'get' . $attribute;
        $setter = 'set' . $attribute;
        $this->assertSame($this->entity, $this->entity->$setter($value));
        $this->assertSame($value, $this->entity->$getter());
    }

    public function getAdderRemover()
    {
        return [
            [
                'Phone',
                'Phones',
                $this->createMock( ContactPhoneInterface::class),
            ],
            [
                'Contact',
                'Contacts',
                $this->createMock( CorporationContactInterface::class),
            ],
        ];
    }

    /**
     * @dataProvider getAdderRemover
     */
    public function testAdderRemover($attribute, $attributes, $value)
    {
        $getters = 'get' . $attributes;
        $adder = 'add' . $attribute;
        $remover = 'remove' . $attribute;
        $this->assertCount(0, $this->entity->$getters());
        $this->assertFalse($this->entity->$remover($value));
        $this->assertTrue($this->entity->$adder($value));
        $this->assertCount(1, $this->entity->$getters());
        $this->assertTrue($this->entity->$remover($value));
        $this->assertCount(0, $this->entity->$getters());
    }
}
