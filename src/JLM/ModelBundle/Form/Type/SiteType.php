<?php

namespace JLM\ModelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('trustee','trustee_select',array('label'=>'Syndic','attr'=>array('class'=>'input-large')))
            ->add('address','jlm_contact_address',array('label'=>'Adresse'))
            ->add('observations',null,array('label'=>'Observations','attr'=>array('class'=>'input-xlarge')))
            ->add('groupNumber',null,array('label'=>'Groupe (RIVP)','required'=>false,'attr'=>array('class'=>'input-mini')))
            ->add('accession','choice',array('label'=>'Accession/Social','choices'=>array('1'=>'Accession','0'=>'Social'),'expanded'=>true,'multiple'=>false))
            ->add('vat',null,array('label'=>'TVA','attr'=>array('class'=>'input-small')))    
            ->add('lodge','jlm_contact_address',array('label'=>'Loge gardien','required'=>false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JLM\ModelBundle\Entity\Site'
        ));
    }

    public function getName()
    {
        return 'jlm_modelbundle_sitetype';
    }
}