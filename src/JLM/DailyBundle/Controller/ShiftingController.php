<?php
namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\ModelBundle\Entity\Technician;
use JLM\DailyBundle\Entity\Shifting;
use JLM\DailyBundle\Entity\Intervention;
use JLM\DailyBundle\Entity\ShiftTechnician;
use JLM\DailyBundle\Form\Type\AddTechnicianType;
use JLM\DailyBundle\Form\Type\ShiftingEditType;

/**
 * Fixing controller.
 *
 * @Route("/shifting")
 */
class ShiftingController extends Controller
{
	/**
	 * List
	 * @Route("/list/{id}", name="shifting_list")
	 * @Secure(roles="ROLE_USER")
	 * @Template()
	 */
	public function listAction(Technician $technician)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$shiftings = $em->getRepository('JLMDailyBundle:ShiftTechnician')->findByTechnician($technician,array('creation'=>'desc'));
		return array(
				'technician'=>$technician,
				'shiftings' => $shiftings
		);
	}
	
	/**
	 * Ajoute un technicien sur une intervention
	 * @Route("/new/{id}", name="shifting_new")
	 * @Secure(roles="ROLE_USER")
	 * @Template()
	 */
	public function newAction(Shifting $shifting)
	{
		$entity = new ShiftTechnician();
		$entity->setBegin(new \DateTime);
		$form   = $this->createForm(new AddTechnicianType(), $entity);
		
		return array(
				'shifting' => $shifting,
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Creates a new ShiftTechnician entity.
	 *
	 * @Route("/create/{id}", name="shifting_create")
	 * @Template()
	 * @Method("POST")
	 * @Secure(roles="ROLE_USER")
	 */
	public function createAction(Request $request, Shifting $shifting)
	{
		$entity  = new ShiftTechnician();
		$form = $this->createForm(new AddTechnicianType(), $entity);
		$form->bind($request);
	
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setCreation(new \DateTime);
			$entity->setShifting($shifting);
			$em->persist($shifting);
			$em->persist($entity);
			$em->flush();
	
			return $this->redirect($request->headers->get('referer'));
		}
	
		return array(
				'entity' => $entity,
				'form'   => $form->createView(),
		);
	}
	
	/**
	 * Displays a form to edit an existing ShiftTechnician entity.
	 *
	 * @Route("/{id}/edit", name="shifting_edit")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function editAction(ShiftTechnician $entity)
	{
	//	$editForm = $this->createForm(new ShiftingEditType(), $entity, array('id'=>$entity->getId()));
		$editForm = $this->get('form.factory')->createNamed('shiftTech'.$entity->getId(),new ShiftingEditType(), $entity);
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Edits an existing InterventionPlanned entity.
	 *
	 * @Route("/{id}/update", name="shifting_update")
	 * @Method("POST")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function updateAction(Request $request, ShiftTechnician $entity)
	{
		$em = $this->getDoctrine()->getManager();
	
		$editForm = $this->createForm(new ShiftingEditType(), $entity);
		$editForm->bind($request);
	
		if ($editForm->isValid()) {
			$begin = $entity->getBegin();
			$end = $entity->getEnd();
			if ($end->format('Hi') != '0000')
			{
				$end->setDate($begin->format('Y'),$begin->format('m'),$begin->format('d'));
				$entity->setEnd($end);
			}
			else
				$entity->setEnd();
			$em->persist($entity);
			$em->flush();
			return $this->redirect($request->headers->get('referer'));
		}
	
		return array(
				'entity'      => $entity,
				'form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Delete an existing ShiftTechnician entity.
	 *
	 * @Route("/{id}/delete", name="shifting_delete")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function deleteAction(ShiftTechnician $entity)
	{
		$intervId = $entity->getShifting()->getId();
		$em = $this->getDoctrine()->getManager();
		$em->remove($entity);
		$em->flush();
	
		return $this->redirect($this->generateUrl('intervention_redirect', array('id' => $intervId, 'act'=>'show')));
	}
}