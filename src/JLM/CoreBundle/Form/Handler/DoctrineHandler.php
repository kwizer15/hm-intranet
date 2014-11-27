<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CoreBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class DoctrineHandler extends FormHandler
{
	/**
	 * @var Request
	 */
	protected $om;
	
	/**
	 * Constructor
	 * @param Form $form
	 * @param Request $request
	 * @param ObjectManager $om
	 */
	public function __construct(Form $form, Request $request, ObjectManager $om)
	{
		parent::__construct($form, $request);
		$this->om = $om;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function onSuccess()
	{
		$entity = $this->form->getData();
		$this->om->persist($entity);
		$this->om->flush();
		
		return true;
	}
}