<?php

namespace JLM\DailyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecuperationEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
      		->add('technician',null,array('label'=>'Technicien'))
      		->add('begin','datepicker',array('label'=>'Date'))
      		->add('shifting',new EquipmentType,array('label'=>'Récupération'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JLM\DailyBundle\Entity\ShiftTechnician'
        ));
    }

    public function getName()
    {
        return 'jlm_dailybundle_recuperationequipmenttype';
    }
}
