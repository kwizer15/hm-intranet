<?php

namespace JLM\ModelBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JLM\ModelBundle\Form\DataTransformer\ProductToIntTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductHiddenType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;
	
	/**
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}
	
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$transformer = new ProductToIntTransformer($this->om);
    	$builder->prependClientTransformer($transformer);
    	
    }

    public function getParent()
    {
    	return 'hidden';
    }
    
    public function getName()
    {
        return 'product_hidden';
    }
    
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected product does not exist',
        ));
    }
}