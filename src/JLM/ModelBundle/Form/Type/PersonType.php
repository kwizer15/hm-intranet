<?php

namespace JLM\ModelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('title','choice',array('label'=>'Titre','choices'=>array('M.'=>'M.','Mme'=>'Mme','Mlle'=>'Mlle'),'attr'=>array('class'=>'input-small')))
        	->add('lastName',null,array('label'=>'Nom'))
            ->add('firstName',null,array('label'=>'Prénom','required'=>false))      
            ->add('fixedPhone',null,array('label'=>'Téléphone fixe','required'=>false,'attr'=>array('class'=>'input-medium')))
            ->add('mobilePhone',null,array('label'=>'Téléphone mobile','required'=>false,'attr'=>array('class'=>'input-medium')))
            ->add('fax',null,array('label'=>'Fax','required'=>false,'attr'=>array('class'=>'input-medium')))
            ->add('email','email',array('label'=>'Adresse e-mail','required'=>false,'attr'=>array('class'=>'input-xlarge')))
        ;
    }

    public function getName()
    {
        return 'jlm_modelbundle_persontype';
    }
    
    public function getDefaultOptions(array $options)
    {
    	return array(
    			'data_class' => 'JLM\ModelBundle\Entity\Person',
    	);
    }
}