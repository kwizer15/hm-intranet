<?php

namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\DailyBundle\Entity\Fixing;
use JLM\DailyBundle\Entity\ShiftTechnician;
use JLM\DailyBundle\Form\Type\AddTechnicianType;
use JLM\DailyBundle\Form\Type\FixingType;
use JLM\DailyBundle\Form\Type\FixingEditType;
use JLM\DailyBundle\Form\Type\FixingCloseType;
use JLM\ModelBundle\Entity\Door;

/**
 * Fixing controller.
 *
 * @Route("/fixing")
 */
class FixingController extends Controller
{
	/**
	 * Finds and displays a InterventionPlanned entity.
	 *
	 * @Route("/list", name="fixing_list")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function listAction()
	{
		$em = $this->getDoctrine()->getManager();
		$entities = $em->getRepository('JLMDailyBundle:Fixing')->getPrioritary();
		return array(
				'entities'      => $entities,
		);
	}
	
	/**
	 * Finds and displays a InterventionPlanned entity.
	 *
	 * @Route("/{id}/show", name="fixing_show")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function showAction(Fixing $entity)
	{
		$st = new ShiftTechnician();
		$st->setBegin(new \DateTime);
		$form   = $this->createForm(new AddTechnicianType(), $st);
		
		return array(
				'entity' => $entity,
				'form_newtech'   => $form->createView(),
		);
		
		
		
		//////////////////////////
		return array(
				'entity'      => $entity,
		);
	}
	
	/**
	 * Displays a form to create a new InterventionPlanned entity.
	 *
	 * @Route("/new/{id}", name="fixing_new")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function newAction(Door $door)
	{
		$entity = new Fixing();
		$entity->setDoor($door);
		$form   = $this->createForm(new FixingType(), $entity);
	
		return array(
				'door' => $door,
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Creates a new InterventionPlanned entity.
	 *
	 * @Route("/create/{id}", name="fixing_create")
	 * @Method("POST")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function createAction(Request $request, Door $door)
	{
		$entity  = new Fixing();
		$form = $this->createForm(new FixingType(), $entity);
		$form->bind($request);
	
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setCreation(new \DateTime);
			$entity->setDoor($door);
			$entity->setContract($door->getActualContract());
			$entity->setPlace($door.'');
			$entity->setPriority(2);
	
			$em->persist($entity);
			$em->flush();
	
			return $this->redirect($this->generateUrl('fixing_show', array('id' => $entity->getId())));
		}
	
		return array(
				'door' => $door,
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Displays a form to edit an existing Fixing entity.
	 *
	 * @Route("/{id}/edit", name="fixing_edit")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function editAction(Fixing $entity)
	{
		$editForm = $this->createForm(new FixingEditType(), $entity);
	
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Edits an existing Fixing entity.
	 *
	 * @Route("/{id}/update", name="fixing_update")
	 * @Method("POST")
	 * @Template("JLMDailyBundle:Fixing:edit.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function updateAction(Request $request, Fixing $entity)
	{
		$em = $this->getDoctrine()->getManager();
			
		$editForm = $this->createForm(new FixingEditType(), $entity);
		$editForm->bind($request);
	
		if ($editForm->isValid())
		{
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('fixing_show', array('id' => $entity->getId())));
		}
	
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Close an existing Fixing entity.
	 *
	 * @Route("/{id}/close", name="fixing_close")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function closeAction(Fixing $entity)
	{
		$form = $this->createForm(new FixingCloseType(), $entity);
	
		return array(
				'entity'      => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Close an existing Fixing entity.
	 *
	 * @Route("/{id}/closeupdate", name="fixing_closeupdate")
	 * @Method("POST")
	 * @Template("JLMDailyBundle:Fixing:close.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function closeupdateAction(Request $request, Fixing $entity)
	{
		$em = $this->getDoctrine()->getManager();
			
		$form = $this->createForm(new FixingCloseType(), $entity);
		$form->bind($request);
	
		if ($form->isValid())
		{
			// Mise à l'arrêt
			if ($entity->getDone()->getId() == 3)
				$entity->getDoor()->setStopped(true);
			
			$entity->setClose(new \DateTime);
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('fixing_show', array('id' => $entity->getId())));
		}
	
		return array(
				'entity'      => $entity,
				'form'   => $form->createView(),
		);
	}
}