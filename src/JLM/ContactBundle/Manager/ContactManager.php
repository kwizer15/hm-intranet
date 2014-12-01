<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Manager;

use JLM\ContactBundle\Form\Type\PersonType;
use JLM\ContactBundle\Form\Type\CompanyType;
use JLM\ContactBundle\Form\Type\AssociationType;
use JLM\ContactBundle\Entity\Person;
use JLM\ContactBundle\Entity\Company;
use JLM\ContactBundle\Entity\Association;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use JLM\CoreBundle\Form\Handler\DoctrineHandler;
/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class ContactManager extends BaseManager
{
	/**
	 * {@inheritdoc}
	 */
	public function getRepository()
	{
		return $this->om->getRepository($this->class);
		return $this->om->getRepository('JLMContactBundle:Contact');
	}
	
	protected function getObjectByType($type)
	{
		$objects = array(
			'person' => 'JLM\\ContactBundle\\Entity\\Person',
			'company' => 'JLM\\ContactBundle\\Entity\\Company',
			'association' => 'JLM\\ContactBundle\\Entity\\Association',
		);
		if (array_key_exists($type, $objects))
		{
			return new $objects[$type];
		}
		
		return null;
	}
	
	protected function getFormType($type = null)
	{
		$objects = array(
				'person' => 'JLM\\ContactBundle\\Form\\Type\\PersonType',
				'company' => 'JLM\\ContactBundle\\Form\\Type\\CompanyType',
				'association' => 'JLM\\ContactBundle\\Form\\Type\\AssociationType',
		);
		if (array_key_exists($type, $objects))
		{
			return new $objects[$type];
		}
	
		return null;
	}
	
	public function getEntity($id = null)
	{
		$entity = $this->getObjectByType($id);
		if (null === $entity)
		{
			$entity = $this->getRepository()->find($id);
			if (!$entity)
			{
				throw $this->createNotFoundException('Unable to find Contact entity.');
			}
		}

		return $entity;
	}
	
	protected function getFormParam($entity)
	{
		$id = $entity->getId();
		$entityType = $entity->getType();
		return array(
			'POST' => array(
				'route'   => 'jlm_contact_contact_create',
				'params' => array('id' => $entityType),
				'label' => 'Créer',
				'type'  => $this->getFormType($entityType),
			),
			'PUT' => array(
				'route' => 'jlm_contact_contact_update',
				'params' => array('id' => $id),
				'label' => 'Modifier',
				'type'  => $this->getFormType($entityType),
			),
		);
	}
}