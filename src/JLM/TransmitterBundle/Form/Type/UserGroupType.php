<?php

namespace JLM\TransmitterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('site','site_hidden')
            ->add('name',null,array('label'=>'Nom du groupe'))
            ->add('model',null,array('label'=>'Type d\'émetteurs'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JLM\TransmitterBundle\Entity\UserGroup',
        	'attr'=>array('class'=>'usergroup'),
        ));
    }

    public function getName()
    {
        return 'jlm_transmitterbundle_usergrouptype';
    }
}