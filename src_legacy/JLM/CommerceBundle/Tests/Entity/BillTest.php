<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Tests\Entity;

use JLM\CommerceBundle\Entity\Bill;
use JLM\CommerceBundle\Entity\BillLine;
use JLM\CommerceBundle\Model\BillInterface;
use JLM\DailyBundle\Entity\Intervention;
use JLM\FeeBundle\Model\FeeInterface;
use JLM\FeeBundle\Model\FeesFollowerInterface;
use JLM\ModelBundle\Entity\Site;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class BillTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Bill
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entity = new Bill();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertPreConditions()
    {
        $this->assertInstanceOf(BillInterface::class, $this->entity);
        $this->assertNull($this->entity->getId());
        $this->assertEquals(0, $this->entity->getAmount());
    }

    public function getAttributes()
    {
        return [
            ['Prelabel', 'Foo',],
            ['Reference', 'Foo',],
            ['AccountNumber', '123456',],
            ['Details', 'Foo',],
            ['Site', 'Foo',],
            ['Property', 'Foo',],
            ['EarlyPayment', 'Foo',],
            ['Penalty', 'Foo',],
            ['Intro', 'Foo',],
            ['Maturity', 30,],
            ['State', 1,],
            ['Discount', 0.50,],
            ['Fee', $this->createMock(FeeInterface::class),],
            ['FeesFollower', $this->createMock(FeesFollowerInterface::class),],
            ['Intervention', $this->createMock(Intervention::class),],
            ['FirstBoost', $this->createMock(\DateTime::class),],
            ['SecondBoost', $this->createMock(\DateTime::class),],
            ['SecondBoostComment', 'Foo',],
            ['SiteObject', $this->createMock(Site::class),],
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

    public function testAddLine()
    {
        $this->entity->addLine($this->getLine(100));
        $this->assertEquals(100, $this->entity->getAmount());

        $this->entity->setDiscount(0.2);
        $this->assertEquals(80, $this->entity->getAmount());

        $this->entity->addLine($this->getLine(50, 2));
        $this->assertEquals(160, $this->entity->getAmount());
    }

    protected function getLine($amount, $qty = 1)
    {
        $line = new BillLine();
        $line->setUnitPrice($amount);
        $line->setQuantity($qty);

        return $line;
    }
}
