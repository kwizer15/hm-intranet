<?php

namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\DailyBundle\Entity\Fixing;
use JLM\DailyBundle\Entity\ShiftTechnician;
use JLM\DailyBundle\Form\Type\AddTechnicianType;
use JLM\DailyBundle\Form\Type\ShiftingEditType;
use JLM\DailyBundle\Form\Type\FixingType;
use JLM\DailyBundle\Form\Type\FixingEditType;
use JLM\DailyBundle\Form\Type\FixingCloseType;
use JLM\DailyBundle\Form\Type\ExternalBillType;
use JLM\DailyBundle\Form\Type\InterventionCancelType;
use JLM\ModelBundle\Entity\Door;
use JLM\ModelBundle\Entity\DoorStop;
use Symfony\Component\HttpFoundation\JsonResponse;
use JLM\CoreBundle\Form\Type\MailType;
use JLM\CoreBundle\Factory\MailFactory;
use JLM\DailyBundle\Builder\FixingTakenMailBuilder;
use JLM\CoreBundle\Builder\MailSwiftMailBuilder;

/**
 * Fixing controller.
 */
class FixingController extends AbstractInterventionController
{
	/**
	 * Finds and displays a InterventionPlanned entity.
	 *
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
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function emailAction(Fixing $entity)
	{
		$mail = MailFactory::create(new FixingTakenMailBuilder($entity));
		$editForm = $this->createForm(new MailType(), $mail);
		$editForm->handleRequest($this->getRequest());
		if ($editForm->isValid())
		{
			$this->get('mailer')->send(MailFactory::create(new MailSwiftMailBuilder($editForm->getData())));
		}
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Finds and displays a InterventionPlanned entity.
	 *
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function showAction(Fixing $entity)
	{
		return $this->show($entity);
	}
	
	/**
	 * Displays a form to create a new InterventionPlanned entity.
	 *
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function newAction(Door $door)
	{
		/*
		 * Voir aussi
		* 	DoorController:stoppedAction
		* 	DefaultController:searchAction
		* @todo A factoriser
		*/
		$entity = new Fixing();
		$entity->setDoor($door);
		$entity->setAskDate(new \DateTime);
		$form = $this->get('form.factory')->createNamed('fixingNew'.$door->getId(),new FixingType(), $entity);
		return array(
				'door' => $door,
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Creates a new InterventionPlanned entity.
	 *
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function createAction(Request $request, Door $door)
	{
		$entity  = new Fixing();
		$entity->setCreation(new \DateTime);
		$entity->setDoor($door);
		$entity->setContract($door->getActualContract());
		$entity->setPlace($door.'');
		$entity->setPriority(2);
		$form = $this->get('form.factory')->createNamed('fixingNew'.$door->getId(),new FixingType(), $entity);
		$form->handleRequest($request);
	
		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();
			if ($request->isXmlHttpRequest())
			{
				return new JsonResponse(array('id' => $entity->getId()));
			}
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
	 * @Template("JLMDailyBundle:Fixing:edit.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function updateAction(Request $request, Fixing $entity)
	{
		$editForm = $this->createForm(new FixingEditType(), $entity);
		$editForm->handleRequest($request);
	
		if ($editForm->isValid())
		{
			$em = $this->getDoctrine()->getManager();
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
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function closeupdateAction(Request $request, Fixing $entity)
	{	
		$form = $this->createForm(new FixingCloseType(), $entity);
		$form->handleRequest($request);
	
		if ($form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			// Mise à l'arrêt
			if ($entity->getDone()->getId() == 3)
			{
				$stop = $entity->getDoor()->getLastStop();
				if ($stop === null)
				{
					$stop = new DoorStop;	
					$stop->setBegin(new \DateTime);
					$stop->setState('Non traitée');
				}
				$stop->setReason($entity->getReport());
				$entity->getDoor()->addStop($stop);
				$em->persist($stop);
			}
			
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
	
	/**
	 * Imprime le rapport d'intervention
	 *
	 * @Secure(roles="ROLE_USER")
	 */
	public function printdayAction(Fixing $entity)
	{
		$response = new Response();
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename=report-'.$entity->getId().'.pdf');
		$response->setContent($this->render('JLMDailyBundle:Fixing:printreport.pdf.php',
				array('entity' => $entity)
			)
		);
	
		return $response;
	}
}