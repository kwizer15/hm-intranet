<?php

namespace JLM\DailyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JLM\DailyBundle\Entity\Standby;
use JLM\DailyBundle\Form\Type\StandbyType;

/**
 * Standby controller.
 */
class StandbyController extends Controller
{
    /**
     * Lists all Standby entities.
     *
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JLMDailyBundle:Standby')->findBy(array(),array('begin'=>'DESC'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Displays a form to create a new Standby entity.
     *
     * @Template()
     */
    public function newAction()
    {
        $entity = new Standby();
        $form   = $this->createForm(new StandbyType(), $entity, array(
        		'method'=>'POST',
        		'action'=>$this->generateUrl('standby_create'),
        ));
        $form->add('submit','submit',array('label'=>'Enregistrer'));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Standby entity.
     *
     * @Template()
     */
    public function createAction(Request $request)
    {
    	
        $entity  = new Standby();
        $form = $this->createForm(new StandbyType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid())
        {
        	$em = $this->getDoctrine()->getManager();
            $em = $this->getDoctrine()->getManager();
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
     * Displays a form to edit an existing Standby entity.
     *
     * @Template()
     */
    public function editAction(Standby $entity)
    {
        $form = $this->get('form.factory')->createNamed('shiftTechNew'.$entity->getId(),new StandbyType(), $entity);
        return array(
            'entity'      => $entity,
            'form'        => $form->createView(),
        );
    }

    /**
     * Edits an existing Standby entity.
     *
     * @Template()
     */
    public function updateAction(Request $request, Standby $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->createNamed('shiftTechNew'.$entity->getId(),new StandbyType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return array(
            'entity'      => $entity,
            'form'        => $form->createView(),
        );
    }

    /**
     * Deletes a Standby entity.
     *
     */
    public function deleteAction(Request $request, Standby $entity)
    {
    	$id = $entity->getId();
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JLMDailyBundle:Standby')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Standby entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('standby'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
