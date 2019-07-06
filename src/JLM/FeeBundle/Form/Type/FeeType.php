<?php

/*
 * This file is part of the JLMFeeBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\FeeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class FeeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('trustee','trustee_select',array('label'=>'Syndic','attr'=>array('class'=>'input-xlarge')))
		->add('address',null ,array('label'=>'Adresse','attr'=>array('class'=>'input-xlarge')))
		->add('prelabel',null,array('label'=>'Libélé','attr'=>array('class'=>'input-xlarge')))
		->add('frequence','choice',array('label'=>'Fréquence','choices'=>array(1=>'Annuelle',2=>'Semestrielle',4=>'Trimestrielle'),'attr'=>array('class'=>'input-normal')))
		->add('vat','entity',array('label'=>'TVA','class'=>'JLM\CommerceBundle\Entity\VAT','attr'=>array('class'=>'input-small')))
		->add('contracts','collection',array('prototype'=>true,'allow_add'=>true,'allow_delete'=>true,'type'=>'jlm_contract_contract_select','label'=>'Contrats'));
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'fee';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'JLM\FeeBundle\Entity\Fee',
		));
	}
}