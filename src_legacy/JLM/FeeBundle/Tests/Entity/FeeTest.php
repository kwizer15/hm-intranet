<?php

/*
 * This file is part of the JLMFeeBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\FeeBundle\Tests\Entity;

use JLM\CommerceBundle\Model\VATInterface;
use JLM\ContractBundle\Model\ContractInterface;
use JLM\FeeBundle\Entity\Fee;
use JLM\ModelBundle\Entity\Trustee;
use JLM\ModelBundle\Entity\Site;
use JLM\ModelBundle\Entity\Door;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class FeeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Fee
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entity = new Fee();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertPreConditions()
    {
        $this->assertInstanceOf('JLM\FeeBundle\Model\FeeInterface', $this->entity);
        $this->assertNull($this->entity->getId());
        $this->assertSame('semestrielle', $this->entity->getFrequenceString());
    }

    public function getGetterSetter()
    {
        return [
            ['Address', 'Foo',],
            ['Prelabel', 'Foo',],
            ['Frequence', 1,],
            ['Vat', $this->createMock(VATInterface::class),],
            ['Trustee', $this->createMock(Trustee::class),],
        ];
    }

    /**
     * @dataProvider getGetterSetter
     *
     * @param $attribute
     * @param $value
     */
    public function testGetterSetter($attribute, $value)
    {
        $getter = 'get' . $attribute;
        $setter = 'set' . $attribute;
        $this->assertSame($this->entity, $this->entity->$setter($value));
        $this->assertSame($value, $this->entity->$getter());
    }

    public function getFrequenceStrings()
    {
        return [
            [1, 'annuelle',],
            [2, 'semestrielle',],
            [4, 'trimestrielle',],
        ];
    }

    /**
     * @dataProvider getFrequenceStrings
     *
     * @param $freq
     * @param $string
     */
    public function testGetFrequenceString($freq, $string)
    {
        $this->entity->setFrequence($freq);
        $this->assertSame($string, $this->entity->getFrequenceString());
    }

    public function getAdderRemover()
    {
        return [
            [
                'Contract',
                'Contracts',
                $this->createMock(ContractInterface::class),
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

    public function testGetContractNumbers()
    {
        $c1 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c2 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c1->expects($this->once())->method('getNumber')->will($this->returnValue('12345'));
        $c2->expects($this->once())->method('getNumber')->will($this->returnValue('6789'));
        $this->entity->addContract($c1);
        $this->entity->addContract($c2);
        $this->assertSame(['12345', '6789'], $this->entity->getContractNumbers());
    }

    public function testGetDoorDescription()
    {
        $door = $this->createMock('JLM\ModelBundle\Entity\Door');
        $door->expects($this->once())->method('getType')->will($this->returnValue('Porte basculante'));
        $door->expects($this->once())->method('getLocation')->will($this->returnValue('Facade'));
        $contract = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $contract->expects($this->once())->method('getDoor')->will($this->returnValue($door));
        $this->entity->addContract($contract);
        $this->assertSame(['Porte basculante / Facade'], $this->entity->getDoorDescription());
    }

    public function testGetYearAmount()
    {
        $c1 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c2 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c1->expects($this->once())->method('getFee')->will($this->returnValue(100.0));
        $c2->expects($this->once())->method('getFee')->will($this->returnValue(200.0));
        $this->entity->addContract($c1);
        $this->entity->addContract($c2);
        $this->assertSame(300.0, $this->entity->getYearAmount());
    }

    public function testGetAmount()
    {
        $c1 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c2 = $this->createMock('JLM\ContractBundle\Model\ContractInterface');
        $c1->expects($this->once())->method('getFee')->will($this->returnValue(100.0));
        $c2->expects($this->once())->method('getFee')->will($this->returnValue(200.0));
        $this->entity->addContract($c1);
        $this->entity->addContract($c2);
        $this->entity->setFrequence(2);
        $this->assertSame(150.0, $this->entity->getAmount());
    }

    public function testGetBillingAddressReturnAddress()
    {
        $address = $this->createMock('JLM\ContactBundle\Model\AddressInterface');
        $trustee = $this->createMock('JLM\ModelBundle\Entity\Trustee');
        $trustee->expects($this->any())->method('getAddress')->will($this->returnValue($address));
        $this->entity->setTrustee($trustee);
        $this->assertSame($address, $this->entity->getBillingAddress());
    }

    public function testGetBillingAddressReturnAddressWithBillingExist()
    {
        $address = $this->createMock('JLM\ContactBundle\Model\AddressInterface');
        $trustee = $this->createMock('JLM\ModelBundle\Entity\Trustee');
        $trustee->expects($this->any())->method('getAddress')->will($this->returnValue($address));
        $this->entity->setTrustee($trustee);
        $billingAddress = $this->createMock('JLM\ContactBundle\Model\AddressInterface');
        $trustee->expects($this->once())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $this->assertSame($address, $this->entity->getBillingAddress());
    }

    public function testGetBillingAddressReturnBilling()
    {
        $address = $this->createMock('JLM\ContactBundle\Model\AddressInterface');
        $trustee = $this->createMock('JLM\ModelBundle\Entity\Trustee');
        $trustee->expects($this->any())->method('getAddress')->will($this->returnValue($address));
        $this->entity->setTrustee($trustee);
        $billingAddress = $this->createMock('JLM\ContactBundle\Model\AddressInterface');
        $city = $this->createMock('JLM\ContactBundle\Model\CityInterface');
        $billingAddress->expects($this->once())->method('getCity')->will($this->returnValue($city));
        $billingAddress->expects($this->once())->method('getStreet')->will($this->returnValue('Foo'));
        $trustee->expects($this->once())->method('getBillingAddress')->will($this->returnValue($billingAddress));
        $this->assertSame($billingAddress, $this->entity->getBillingAddress());
    }

    public function testGetGroup()
    {
        $number = '12345';
        $site = $this->createMock(Site::class);
        $site->expects($this->once())->method('getGroupNumber')->will($this->returnValue($number));
        $door = $this->createMock(Door::class);
        $door->expects($this->once())->method('getAdminitrator')->will($this->returnValue($site));
        $contract = $this->createMock(ContractInterface::class);
        $contract->expects($this->once())->method('getDoor')->will($this->returnValue($door));
        $this->entity->addContract($contract);
        $this->assertSame($number, $this->entity->getGroup());
    }
}
