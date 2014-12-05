<?php

namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\DailyBundle\Entity\Work;
use JLM\DailyBundle\Form\Type\WorkType;
use JLM\DailyBundle\Form\Type\WorkEditType;
use JLM\DailyBundle\Form\Type\WorkCloseType;
use JLM\DailyBundle\Entity\ShiftTechnician;
use JLM\DailyBundle\Form\Type\AddTechnicianType;
use JLM\DailyBundle\Form\Type\ShiftingEditType;
use JLM\DailyBundle\Form\Type\ExternalBillType;
use JLM\DailyBundle\Form\Type\InterventionCancelType;
use JLM\ModelBundle\Entity\Door;
use JLM\CommerceBundle\Entity\QuoteVariant;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Work controller.
 *
 * @Route("/work")
 */
class WorkController extends AbstractInterventionController
{
	/**
	 * @Route("/", name="jlm_daily_work")
	 */
	public function indexAction()
	{
		$request = $this->getRequest();
		if (!$request->isXmlHttpRequest())
		{
			return $this->redirect($this->generateUrl('work_list'));
		}
		
		$query = $request->get('q','');
		$pageLimit = $request->get('page_limit',10);
		$om = $this->get('doctrine')->getManager();
		$repo = $om->getRepository('JLMDailyBundle:Work');
		$entities = $repo->getArray($query, $pageLimit);
		
		return new JsonResponse(array('works' => $entities));
		
	}
	
	/**
	 * @Route("/{id}", name="jlm_daily_work_show")
	 */
	public function indexAction($id)
	{
		$request = $this->getRequest();
		if (!$request->isXmlHttpRequest())
		{
			return $this->redirect($this->generateUrl('work_show', array('id'=>$id)));
		}
	
		$om = $this->get('doctrine')->getManager();
		$repo = $om->getRepository('JLMDailyBundle:Work');
		$entity = $repo->getByIdToArray($id);
	
		return new JsonResponse(array($entity));
	
	}
	
	/**
	 * @Route("/list", name="work_list")
	 * @Route("/list/{page}", name="work_list_page")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function listAction($page = 1)
	{
		return $this->pagination('JLMDailyBundle:Work','Opened',$page,10,'work_list_page');
	}
	
	/**
	 * Finds and displays a Work entity.
	 *
	 * @Route("/{id}/show", name="work_show")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function showAction(Work $entity)
	{
		return $this->show($entity);
	}
	
	/**
	 * Displays a form to create a new Work entity.
	 *
	 * @Route("/new/door/{id}", name="work_new_door")
	 * @Template("JLMDailyBundle:Work:new.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function newdoorAction(Door $door)
	{
		$entity = new Work();
		$entity->setDoor($door);
		$entity->setPlace($door.'');
		$form   = $this->createForm(new WorkType(), $entity);
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Displays a form to create a new Work entity.
	 *
	 * @Route("/new", name="work_new")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function newAction()
	{
		$entity = new Work();
		$form   = $this->createForm(new WorkType(), $entity);
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Displays a form to create a new Work entity.
	 *
	 * @Route("/new/quote/{id}", name="work_new_quote")
	 * @Template("JLMDailyBundle:Work:new.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function newquoteAction(QuoteVariant $quote)
	{
		$entity = new Work();
		$entity->setQuote($quote);
		$door = $quote->getQuote()->getDoor();
		$entity->setDoor($door);
		$entity->setPlace($quote->getQuote()->getDoorCp());
		$entity->setReason($quote->getIntro());
		$contact = $quote->getQuote()->getContact();
		if ($contact === null)
			$entity->setContactName($quote->getQuote()->getContactCp());
		else
		{
			$entity->setContactName($contact->getPerson()->getName().' ('.$contact->getRole().')');
			$mobilePhone = $contact->getPerson()->getMobilePhone();
			$fixedPhone = $contact->getPerson()->getFixedPhone();
			$email = $contact->getPerson()->getEmail();
			$phones = '';
			if ($mobilePhone != null)
				$phones .= $mobilePhone;
			if ($fixedPhone != null)
			{
				if ($phones != '')
					$phones .= chr(10);
				$phones .= $fixedPhone;
			}
			if ($email != null)
				$entity->setContactEmail($email);
			$entity->setContactPhones($phones);
		}
		$form   = $this->createForm(new WorkType(), $entity);
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Creates a new Work entity.
	 *
	 * @Route("/create", name="work_create")
	 * @Method("POST")
	 * @Template("JLMDailyBundle:Work:new.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function createAction(Request $request)
	{
		$entity  = new Work();
		
		$form = $this->createForm(new WorkType(), $entity);
		$entity->setCreation(new \DateTime);
		$entity->setPriority(4);
		$form->handleRequest($request);
		$entity->setContract($entity->getDoor()->getActualContract());

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();

	
			$em->persist($entity);
			$em->flush();
	
			return $this->redirect($this->generateUrl('work_show', array('id' => $entity->getId())));
		}
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Displays a form to edit an existing Work entity.
	 *
	 * @Route("/{id}/edit", name="work_edit")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function editAction(Work $entity)
	{
		$editForm = $this->createForm(new WorkEditType(), $entity);
	
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Edits an existing Work entity.
	 *
	 * @Route("/{id}/update", name="work_update")
	 * @Method("POST")
	 * @Template("JLMDailyBundle:Work:edit.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function updateAction(Request $request, Work $entity)
	{
		$em = $this->getDoctrine()->getManager();
			
		$editForm = $this->createForm(new WorkEditType(), $entity);
		$editForm->handleRequest($request);
	
		if ($editForm->isValid())
		{
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('work_show', array('id' => $entity->getId())));
		}
	
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Close an existing Work entity.
	 *
	 * @Route("/{id}/close", name="work_close")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function closeAction(Work $entity)
	{
		$form = $this->createForm(new WorkCloseType(), $entity);
	
		return array(
				'entity'      => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Close an existing Work entity.
	 *
	 * @Route("/{id}/closeupdate", name="work_closeupdate")
	 * @Method("POST")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function closeupdateAction(Request $request, Work $entity)
	{	
		$form = $this->createForm(new WorkCloseType(), $entity);
		$form->handleRequest($request);
	
		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			if ($entity->getObjective()->getId() == 1)  // Mise en service
			{
				$stop = $entity->getDoor()->getLastStop();
				if ($stop !== null)
				{
					$stop->setEnd(new \DateTime);
					$em->persist($stop);
				}
				$em->persist($entity->getDoor());
			}
			$entity->setClose(new \DateTime);
			$entity->setMustBeBilled($entity->getQuote() !== null);
			$em->persist($entity);
			$em->flush();
			return $this->redirect($this->generateUrl('work_show', array('id' => $entity->getId())));
		}
	
		return array(
				'entity'      => $entity,
				'form'   => $form->createView(),
		);
	}
}