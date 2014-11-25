<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('title','choice',array('label'=>'Titre', 'choices'=>array('M.'=>'M.','Mme'=>'Mme','Mlle'=>'Mlle')))
        	->add('lastName',null,array('label'=>'Nom'))
            ->add('firstName',null,array('label'=>'Prénom', 'required'=>false))
            ->add('contact', 'jlm_contact_contact', array('data_class' => 'JLM\ContactBundle\Entity\Person'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jlm_contact_person';
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
        ->setDefaults(array(
            'data_class' => 'JLM\ContactBundle\Entity\Person',
        ));
    }
}
