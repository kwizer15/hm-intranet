<?php

namespace JLM\ModelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DoorStopEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('reason','textarea',array('label'=>'Raison'))
        	->add('state','textarea',array('label'=>'État'))
        ;
    }

    public function getName()
    {
        return 'jlm_modelbundle_doorstopedittype';
    }
    
    public function getDefaultOptions(array $options)
    {
    	return array(
    			'data_class' => 'JLM\ModelBundle\Entity\DoorStop',
    	);
    }
}
