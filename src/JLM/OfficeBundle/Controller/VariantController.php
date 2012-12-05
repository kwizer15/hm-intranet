<?php

namespace JLM\OfficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\ModelBundle\Entity\Mail;
use JLM\ModelBundle\Form\Type\MailType;
use JLM\OfficeBundle\Entity\Quote;
use JLM\OfficeBundle\Entity\QuoteVariant;
use JLM\OfficeBundle\Form\Type\QuoteVariantType;
use JLM\OfficeBundle\Entity\QuoteLine;



/**
 * Quote controller.
 *
 * @Route("/quote/variant")
 */
class VariantController extends Controller
{
	/**
     * Displays a form to create a new Variant entity.
     *
     * @Route("/{id}/new", name="variant_new")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function newAction(Quote $quote)
    {
        $entity = new QuoteVariant();
        $entity->setQuote($quote);
        
        $entity->setCreation(new \DateTime);
        $l = new QuoteLine;
        $l->setVat($quote->getVat());
        $entity->addLine($l);
        $form   = $this->createForm(new QuoteVariantType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
    
    /**
     * Creates a new Variant entity.
     *
     * @Route("/create", name="variant_create")
     * @Method("post")
     * @Template("JLMOfficeBundle:Variant:new.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function createAction(Request $request)
    {
    	$entity  = new QuoteVariant();
    	$form    = $this->createForm(new QuoteVariantType(), $entity);
    	$form->bind($request);
    
    	if ($form->isValid())
    	{
    		$em = $this->getDoctrine()->getEntityManager();
    		$lines = $entity->getLines();
    		foreach ($lines as $line)
    		{
    			$line->setVariant($entity);
    			$em->persist($line);
    		}
			$number = $em->getRepository('JLMOfficeBundle:QuoteVariant')->getCount($entity->getQuote())+1;
			$entity->setVariantNumber($number);
    		$em->persist($entity);
    		$em->flush();
    		return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
    	}
    
    	return array(
	    	'entity' => $entity,
	    	'form'   => $form->createView()
    	);
    }
    
	/**
	 * Displays a form to edit an existing QuoteVariant entity.
	 *
	 * @Route("/{id}/edit", name="variant_edit")
	 * @Template()
	 * @Secure(roles="ROLE_USER")
	 */
	public function editAction(QuoteVariant $entity)
	{
		// Si le devis est déjà validé, on empèche quelconque modification
		if ($entity->getState())
			return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
		$editForm = $this->createForm(new QuoteVariantType(), $entity);
		return array(
				'entity'      => $entity,
				'edit_form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Edits an existing QuoteVariant entity.
	 *
	 * @Route("/{id}/update", name="variant_update")
	 * @Method("post")
	 * @Template("JLMOfficeBundle:Variant:edit.html.twig")
	 * @Secure(roles="ROLE_USER")
	 */
	public function updateAction(Request $request, QuoteVariant $entity)
	{
		 
		// Si le devis est déjà validé, on empèche quelconque odification
		if ($entity->getState())
			return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
	
		$originalLines = array();
		foreach ($entity->getLines() as $line)
			$originalLines[] = $line;
		$editForm = $this->createForm(new QuoteVariantType(), $entity);
		$editForm->bind($request);
	
		if ($editForm->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($entity);
			foreach ($entity->getLines() as $key => $line)
			{
	
				// Nouvelles lignes
				$line->setVariant($entity);
				$em->persist($line);
	
				// On vire les anciennes
				foreach ($originalLines as $key => $toDel)
					if ($toDel->getId() === $line->getId())
					unset($originalLines[$key]);
			}
			foreach ($originalLines as $line)
			{
				$em->remove($line);
			}
			$em->flush();
			return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
		}
	
		return array(
				'entity'      => $entity,
				'edit_form'   => $editForm->createView(),
		);
	}
	
	/**
	 * Note QuoteVariant as ready to send.
	 *
	 * @Route("/{id}/ready", name="variant_ready")
	 * @Secure(roles="ROLE_USER")
	 */
	public function readyAction(Request $request, QuoteVariant $entity)
	{
		if ($entity->getState() == 0)
			$entity->setState(1);
		return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
	}
}