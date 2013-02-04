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
use JLM\OfficeBundle\Entity\Bill;
use JLM\OfficeBundle\Entity\QuoteVariant;
use JLM\OfficeBundle\Form\Type\BillType;
use JLM\OfficeBundle\Entity\BillLine;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;



/**
 * Bill controller.
 *
 * @Route("/bill")
 */
class BillController extends Controller
{
    /**
     * Lists all Bill entities.
     *
     * @Route("/", name="bill")
     * @Route("/page/{page}", name="bill_page")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function indexAction($page = 1)
    {
    	$limit = 10;
        $em = $this->getDoctrine()->getEntityManager();
           
        $nb = $em->getRepository('JLMOfficeBundle:Bill')->getTotal();
        $nbPages = ceil($nb/$limit);
        $nbPages = ($nbPages < 1) ? 1 : $nbPages;
        $offset = ($page-1) * $limit;
        if ($page < 1 || $page > $nbPages)
        {
        	throw $this->createNotFoundException('Page insexistante (page '.$page.'/'.$nbPages.')');
        }
        
        $entities = $em->getRepository('JLMOfficeBundle:Bill')->findBy(
        		array(),
        		array('number'=>'desc'),
        		$limit,
        		$offset
        );
        
        return array(
        		'entities' => $entities,
        		'page'     => $page,
        		'nbPages'  => $nbPages,
        );
    }
    
    /**
     * Finds and displays a Bill entity.
     *
     * @Route("/{id}/show", name="bill_show")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function showAction(Bill $entity)
    {
        return array('entity'=> $entity);
    }
    
    /**
     * Displays a form to create a new Bill entity.
     *
     * @Route("/new", name="bill_new")
     * 
     * @Template()
     * @Secure(roles="ROLE_USER")
     * Route("/new/door/{id}", name="bill_new_quotevariant")
     */
    public function newAction(QuoteVariant $variant = null)
    {
        $entity = new Bill();
        $entity->setCreation(new \DateTime);
		$em = $this->getDoctrine()->getEntityManager();
		$vat = $em->getRepository('JLMModelBundle:VAT')->find(1)->getRate();
		
//	if (!empty($door))
//	{
//		$entity->setDoor($door);
//		$entity->setDoorCp($door.'');
//		$contract = $door->getActualContract();
//		$trustee = (empty($contract)) ? $door->getSite()->getTrustee() : $contract->getTrustee();		
//		$entity->setTrustee($trustee);
//		$entity->setTrusteeName($trustee->getName());
//		$entity->setTrusteeAddress($trustee->getAddress().'');
//		$entity->setVat($door->getSite()->getVat()->getRate());
//	}
//	else
//	{
			$entity->setVat($vat);
//	}
      //  $entity->addLine(new QuoteLine);
        $entity->setVatTransmitter($vat);
        $form   = $this->createForm(new BillType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Bill entity.
     *
     * @Route("/create", name="bill_create")
     * @Method("post")
     * @Template("JLMOfficeBundle:Bill:new.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function createAction(Request $request)
    {
        $entity  = new Bill();
        $form    = $this->createForm(new BillType(), $entity);
        $form->bind($request);
		
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $number = $entity->getCreation()->format('ym');
            $n = ($em->getRepository('JLMOfficeBundle:Bill')->getLastNumber() + 1);
            for ($i = strlen($n); $i < 4 ; $i++)
            	$number.= '0';
            $number.= $n;
            $entity->setNumber($number);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));  
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Bill entity.
     *
     * @Route("/{id}/edit", name="bill_edit")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Bill $entity)
    {
    	// Si le devis est déjà validé, on empèche quelconque modification
    	if ($entity->getState())
    		return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
        $editForm = $this->createForm(new BillType(), $entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Bill entity.
     *
     * @Route("/{id}/update", name="bill_update")
     * @Method("post")
     * @Template("JLMOfficeBundle:Bill:edit.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, Bill $entity)
    {
    	
    	// Si le devis est déjà validé, on empèche quelconque modification
    	if ($entity->getState())
    		return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
    	 
        $editForm = $this->createForm(new BillType(), $entity);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
        	$em = $this->getDoctrine()->getEntityManager();
        	$em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }  
}
