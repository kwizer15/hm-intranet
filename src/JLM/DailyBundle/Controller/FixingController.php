<?php

namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\DailyBundle\Entity\Fixing;
use JLM\DailyBundle\Form\Type\FixingType;
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
	 * @Template("JLMDailyBundle:Fixing:new.html.twig")
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
}