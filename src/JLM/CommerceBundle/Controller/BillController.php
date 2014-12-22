<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JLM\CommerceBundle\Factory\BillFactory;
use JLM\CommerceBundle\Entity\Bill;
use JLM\CommerceBundle\Entity\BillLine;
use JLM\CommerceBundle\Form\Type\BillType;
use JLM\CommerceBundle\Model\BillInterface;
use JLM\CommerceBundle\Entity\QuoteVariant;
use JLM\CommerceBundle\Builder\VariantBillBuilder;
use JLM\DailyBundle\Entity\Intervention;
use JLM\DailyBundle\Builder\WorkBillBuilder;
use JLM\DailyBundle\Builder\InterventionBillBuilder;
use JLM\DailyBundle\Entity\Work;
use JLM\ModelBundle\Entity\Door;
use JLM\ModelBundle\Builder\DoorBillBuilder;
use JLM\DailyBundle\Form\Type\ExternalBillType;
use JLM\CoreBundle\Entity\Search;


/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class BillController extends Controller
{
	/**
	 * List bills
	 */
	public function indexAction()
	{
		$manager = $this->container->get('jlm_commerce.bill_manager');
		$manager->secure('ROLE_USER');
		$request = $manager->getRequest();
		$states = array(
			'all' => 'All',
			'in_seizure' => 'InSeizure',
			'sended' => 'Sended',
			'payed' => 'Payed',
			'canceled' => 'Canceled'
		);
		$state = $request->get('state');
		$state = (!array_key_exists($state, $states)) ? 'all' : $state;
		$method = $states[$state];
		$functionCount = 'getCount'.$method;
		$functionDatas = 'get'.$method;
		
		return $manager->renderResponse('JLMCommerceBundle:Bill:index.html.twig',
				$manager->pagination($functionCount, $functionDatas, 'bill', array('state' => $state))
		);
	}
    
    /**
     * Finds and displays a Bill entity.
     */
    public function showAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.bill_manager');
    	$manager->secure('ROLE_USER');
    	
        return $manager->renderResponse('JLMCommerceBundle:Bill:show.html.twig',array('entity'=> $manager->getEntity($id)));
    }
    
    /**
     * Displays a form to create a new Bill entity.
     *
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function newAction()
    {
        $entity = new Bill();
        $entity->setCreation(new \DateTime);
		$em = $this->getDoctrine()->getManager();
		$vat = $em->getRepository('JLMCommerceBundle:VAT')->find(1)->getRate();
		$entity->setVat($vat);
		$entity = $this->finishNewBill($entity);
        $entity->addLine(new BillLine());
        $form   = $this->createNewForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
    
    /**
     * Displays a form to create a new Bill entity.
     *
     * @Template("JLMCommerceBundle:Bill:new.html.twig")
     * @Secure(roles="ROLE_USER")
     * @deprecated No used
     */
    public function newdoorAction(Door $door)
    {
    	$entity = BillFactory::create(new DoorBillBuilder($door));
    	$form   = $this->createNewForm($entity);
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	);
    }

    /**
     * Displays a form to create a new Bill entity.
     * 
     * @Template("JLMCommerceBundle:Bill:new.html.twig")
     * @Secure(roles="ROLE_USER")
     * @deprecated
     */
    public function newquoteAction(QuoteVariant $quote)
    {
        $entity = BillFactory::create(new VariantBillBuilder($quote));
    	$form   = $this->createNewForm($entity);
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	);
    }
    
    /**
     * Displays a form to create a new Bill entity.
     *
     * @Template("JLMCommerceBundle:Bill:new.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function newinterventionAction(Intervention $interv)
    {
        $em = $this->getDoctrine()->getManager();
        $options = array(
            'penalty' => (string)$em->getRepository('JLMCommerceBundle:PenaltyModel')->find(1),
            'property' => (string)$em->getRepository('JLMCommerceBundle:PropertyModel')->find(1),
            'earlyPayment' => (string)$em->getRepository('JLMCommerceBundle:EarlyPaymentModel')->find(1),
        );
        $builder = ($interv instanceof Work) ? (($interv->getQuote() !== null) ? new WorkBillBuilder($interv, $options) : null) : null;
        $builder = ($builder === null) ? new InterventionBillBuilder($interv, $options) : $builder;
        $entity = BillFactory::create($builder);

    	$form   = $this->createNewForm($entity);
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	);
    }
    
    /**
     * Finish Bill
     */
    public function finishNewBill(Bill $entity)
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity->setPenalty($em->getRepository('JLMCommerceBundle:PenaltyModel')->find(1).'');
    	$entity->setProperty($em->getRepository('JLMCommerceBundle:PropertyModel')->find(1).'');
    	$entity->setEarlyPayment($em->getRepository('JLMCommerceBundle:EarlyPaymentModel')->find(1).'');
    	$entity->setMaturity(30);
    	return $entity;
    }
    
    /**
     * Creates a new Bill entity.
     *
     * @Template("JLMCommerceBundle:Bill:new.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function createAction(Request $request)
    {
        $entity  = new Bill();
        $em = $this->getDoctrine()->getManager();
        $vat = $em->getRepository('JLMCommerceBundle:VAT')->find(1)->getRate();
        $entity->setVatTransmitter($vat);
        $form    = $this->createNewForm($entity);
        $form->handleRequest($request);
		
        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
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
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Bill $entity)
    {
    	// Si le devis est déjà validé, on empèche quelconque modification
    	if ($entity->getState())
    	{
    		return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
    	}
        $editForm = $this->createEditForm($entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );

    }

    /**
     * Edits an existing Bill entity.
     *
     * @Template("JLMCommerceBundle:Bill:edit.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, Bill $entity)
    {
    	// Si la facture est déjà validé, on empèche quelconque modification
    	if ($entity->getState())
    	{
    		return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
    	}
    	 
    	$originalLines = array();
    	foreach ($entity->getLines() as $line)
    	{
    	   $originalLines[] = $line;
    	}
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid())
        {
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($entity);
	       	$lines = $entity->getLines();
	       	foreach ($lines as $key => $line)
	       	{
	       		$line->setBill($entity);
	       		$em->persist($line);
	       		foreach ($originalLines as $key => $toDel)
	       		{
	       			if ($toDel->getId() === $line->getId())
	       			{
	       				unset($originalLines[$key]);
	       			}
	       		}
	       	}
	       	foreach ($originalLines as $line)
	       	{
	       		$em->remove($line);
	       	}
            $em->flush();
            
            return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    } 
    
    /**
     * Imprimer la facture
     *
     * @Secure(roles="ROLE_USER")
     */
    public function printAction(Bill $entity)
    {
        return $this->printer($entity);
    }
    
    /**
     * Imprimer un duplicata de facture
     *
     * @Secure(roles="ROLE_USER")
     */
    public function printduplicateAction(Bill $entity)
    {
    	return $this->printer($entity, true);
    }
    
    private function printer(BillInterface $entity, $duplicate = false)
    {
        $filename = $entity->getNumber();
        if ($duplicate)
        {
            $filename .= '-duplicata';
        }
        
        $filename .= '.pdf';
            
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename='.$filename);
        $response->setContent($this->render('JLMCommerceBundle:Bill:print.pdf.php',array('entities'=>array($entity),'duplicate'=>$duplicate)));
        
        return $response;
    }
    
    /**
     * Imprimer la liste des factures à faire
     *
     * @Secure(roles="ROLE_USER")
     */
    public function printlistAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$entities = $em->getRepository('JLMDailyBundle:Intervention')->getToBilled();
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/pdf');
    	$response->headers->set('Content-Disposition', 'inline; filename=factures-a-faire.pdf');
    	$response->setContent($this->render('JLMCommerceBundle:Bill:printlist.pdf.php',array('entities'=>$entities)));
    
    	//   return array('entity'=>$entity);
    	return $response;
    }
    
    /**
     * Note Bill as ready to send.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function readyAction(Bill $entity)
    {
    	return $this->stateChange($entity, 1);
    }
    
    /**
     * Note Bill as been send.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function sendAction(Bill $entity)
    {
    	if ($entity->getState() != 1)
    	{
    		$entity->setState(1);
    	}
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($entity);
    	$em->flush();
    	
    	return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    /**
     * Note Bill as been canceled.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function cancelAction(Bill $entity)
    {
    	return $this->stateChange($entity, -1);
    }
    
    /**
     * Note Bill retour à la saisie.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function backAction(Bill $entity)
    {
    	return $this->stateChange($entity, 0);
    }
    
    /**
     * Note Bill réglée.
     *
     * @Secure(roles="ROLE_USER")
     */
    public function payedAction(Bill $entity)
    {
    	return $this->stateChange($entity, 2);
    }
    
    private function stateChange(Bill $entity, $newState)
    {
        switch ($newState)
        {
        	case 1:
        	    $redirect = ($entity->getState() < 0);    
        	    $set = ($entity->getState() < $newState);
        	    break;
        	case 2:
        	    $redirect = ($entity->getState() > 1);
        	    $set = ($entity->getState() == 1);
        	    break;
        	case -1:
        	case 0:
        	    $redirect = ($entity->getState() < 1);
        	    $set = ($entity->getState() > $newState);
        	    break;

        }
        if ($redirect)
        {
            return $this->redirect($this->generateUrl('bill_show', array('id' => $entity->getId())));
        }
        if ($set)
        {
            $entity->setState($newState);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
         
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    /**
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function todoAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$list = $em->getRepository('JLMDailyBundle:Intervention')->getToBilled();
    	$forms_externalBill = array();
    	foreach ($list as $interv)
    	{
    		$forms_externalBill[] = $this->get('form.factory')->createNamed('externalBill'.$interv->getId(),new ExternalBillType(), $interv)->createView();
    	}
    	return array(
    			'entities'=>$list,
    			'forms_externalbill' => $forms_externalBill,
    	);
    }
    
    /**
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function toboostAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$bills = $em->getRepository('JLMCommerceBundle:Bill')->getToBoost();
    	
    	return array('entities'=>$bills);
    }
    
    /**
     * Imprimer le courrier de relance 
     *
     * @Secure(roles="ROLE_USER")
     */
    public function printboostAction(Bill $entity)
    {
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/pdf');
    	$response->headers->set('Content-Disposition', 'inline; filename='.$entity->getNumber().'.pdf');
    	$response->setContent($this->render('JLMCommerceBundle:Bill:printboost.pdf.php',array('entities'=>array($entity))));
    
    	//   return array('entity'=>$entity);
    	return $response;
    }
    
    /**
     * Noter relance effectuée
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function boostokAction(Bill $entity)
    {
        $date = new \DateTime;
    	if ($entity->getFirstBoost() === null)
    	{
    		$entity->setFirstBoost($date);
    	}
    	else
    	{
    		$entity->setSecondBoost($date);
    	}
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($entity);
    	$em->flush();
    	
    	return $this->redirect($this->generateUrl('bill_toboost'));
    }
    
    /**
     * @Template()
     */
    public function searchAction(Request $request)
    {
    	$formData = $request->get('jlm_core_search');
    	 
    	if (is_array($formData) && array_key_exists('query', $formData))
    	{
    		$em = $this->getDoctrine()->getManager();
    		$entity = new Search();
    		$query = $formData['query'];
    		$entity->setQuery($query);
    		return array(
    				'results' => $em->getRepository('JLMCommerceBundle:Bill')->search($entity),
    				'query' => $entity->getQuery(),
    		);
    	}
    	 
    	return array();
    }
    
    private function createNewForm(BillInterface $entity)
    {
        return $this->createForm(new BillType(), $entity);
    }
    
    private function createEditForm(BillInterface $entity)
    {
        return $this->createForm(new BillType(), $entity);
    }
}
