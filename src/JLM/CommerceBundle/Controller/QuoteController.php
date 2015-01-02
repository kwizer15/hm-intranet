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

use JMS\SecurityExtraBundle\Annotation\Secure;
use JLM\CommerceBundle\Entity\QuoteLine;
use JLM\CommerceBundle\Entity\Quote;
use JLM\CommerceBundle\Form\Type\QuoteType;
use JLM\CommerceBundle\Model\QuoteInterface;
use JLM\DefaultBundle\Entity\Search;
use JLM\DefaultBundle\Form\Type\SearchType;
use JLM\ModelBundle\Entity\Door;
use JLM\ModelBundle\Entity\Mail;
use JLM\ModelBundle\Form\Type\MailType;
use JLM\OfficeBundle\Entity\AskQuote;
use JLM\CommerceBundle\JLMCommerceEvents;
use JLM\CommerceBundle\Event\QuoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Quote controller.
 *
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class QuoteController extends Controller
{
    /**
     * Lists all Quote entities.
     */
    public function indexAction($state = null)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$request = $manager->getRequest();
    	$states = array(
    			'all' => 'All',
    			'in_seizure' => 'InSeizure',
    			'waiting' => 'Waiting',
    			'sended' => 'Sended',
    			'given' => 'Given',
    			'canceled' => 'Canceled'
    	);
    	$state = $request->get('state');
    	$state = (!array_key_exists($state, $states)) ? 'all' : $state;
    	$method = $states[$state];
    	$functionCount = 'getCount'.$method;
    	$functionDatas = 'get'.$method;
    	
    	return $manager->renderResponse('JLMCommerceBundle:Quote:index.html.twig',
    			$manager->pagination($functionCount, $functionDatas, 'quote', array('state' => $state))
    	);
    }

    /**
     * Finds and displays a Quote entity.
     */
    public function showAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	
        return $manager->renderResponse('JLMCommerceBundle:Quote:show.html.twig', 
        		array('entity'=> $manager->getEntity($id))
        );
    }
    
    /**
     * Nouveau devis depuis un demande de devis 
     */
    public function newAction()
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$form = $manager->createForm('new');
    	
    	if ($manager->getHandler($form)->process())
    	{
    		$entity = $form->getData();
    		$manager->dispatch(JLMCommerceEvents::QUOTE_AFTER_PERSIST, new QuoteEvent($entity, $manager->getRequest()));
    	
    		return $manager->redirect('quote_show', array('id' => $form->getData()->getId()));
    	}
    	
    	return $manager->renderResponse('JLMCommerceBundle:Quote:new.html.twig', array(
    			'form'   => $form->createView()
    	));
    }

    /**
     * Displays a form to edit an existing Quote entity.
     */
    public function editAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$entity = $manager->getEntity($id);
    	$manager->assertState($entity, array(0));
    	$form = $manager->createForm('edit', array('entity' => $entity));
    	if ($manager->getHandler($form)->process())
    	{
    		return $manager->redirect('quote_show', array('id' => $form->getData()->getId()));
    	}
    	
        return $manager->renderResponse('JLMCommerceBundle:Quote:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $form->createView(),
        ));
    }
     
    /**
     * Resultats de la barre de recherche.
     */
    public function searchAction()
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	
    	return $manager->renderSearch('JLMCommerceBundle:Quote:search.html.twig');
    }
    
    /**
     * Imprimer toute les variantes
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function printAction(Quote $entity)
    {
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/pdf');
    	$response->headers->set('Content-Disposition', 'inline; filename='.$entity->getNumber().'.pdf');
    	$response->setContent($this->render('JLMCommerceBundle:Quote:quote.pdf.php',array('entities'=>$entity->getVariants())));
    		
    	//   return array('entity'=>$entity);
    	return $response;
    }
    
    /**
     * Imprimer la chemise
     *
     * @Secure(roles="ROLE_USER")
     */
    public function jacketAction(Quote $entity)
    {
    	$response = new Response();
    	$response->headers->set('Content-Type', 'application/pdf');
    	$response->headers->set('Content-Disposition', 'inline; filename='.$entity->getNumber().'.pdf');
    	$response->setContent($this->render('JLMCommerceBundle:Quote:jacket.pdf.php',array('entity'=>$entity)));
    
    	//   return array('entity'=>$entity);
    	return $response;
    }
    
    /**
     * Mail
     * 
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function mailAction(Quote $entity)
    {
    	if ($entity->getState() < 1)
    		return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getQuote()->getId())));
    	$mail = new Mail();
    	$mail->setSubject('Devis n°'.$entity->getNumber());
    	$mail->setFrom('commerce@jlm-entreprise.fr');
    	$mail->setBody($this->renderView('JLMCommerceBundle:Quote:email.txt.twig', array('door' => $entity->getDoorCp())));
    	$mail->setSignature($this->renderView('JLMCommerceBundle:QuoteVariant:emailsignature.txt.twig', array('name' => $entity->getFollowerCp())));
    	if ($entity->getContact())
    	{
    		if ($entity->getContact()->getPerson())
    		{
    			if ($entity->getContact()->getPerson()->getEmail())
    			{
    				$mail->setTo($entity->getContact()->getPerson()->getEmail());
    			}
    		}
    	}
    	$form   = $this->createForm(new MailType(), $mail);
    
    	return array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	);
    }
    
    /**
     * Send by mail a QuoteVariant entity.
     * 
     * @Secure(roles="ROLE_USER")
     */
    public function sendmailAction(Request $request, Quote $entity)
    {
    	if ($entity->getState() < 1)
    	{
    		return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getId())));
    	}
    	
    	// Message
    	$mail = new Mail;
    	$form = $this->createForm(new MailType(), $mail);
    	$form->handleRequest($request);
    		
    	if ($form->isValid())
    	{
    		$mail->setBcc('commerce@jlm-entreprise.fr');
    			
    		$message = $mail->getSwift();
    		$message->setReadReceiptTo('commerce@jlm-entreprise.fr');
    		foreach ($entity->getVariants() as $variant)
    			if ($variant->getState() > 0)
    			{
		    		$message->attach(\Swift_Attachment::newInstance(
		    				$this->render('JLMCommerceBundle:Quote:quote.pdf.php',array('entities'=>array($variant))),
		    				$variant->getNumber().'.pdf','application/pdf'
		    		))
		    		;
		    		if ($variant->getState() < 3)
		    		{
		    			$variant->setState(3);
		    		}
    			}
    			if ($entity->getVat() == $entity->getVatTransmitter())
    			{
    				$message->attach(\Swift_Attachment::fromPath(
						$this->get('kernel')->getRootDir().'/../web/bundles/jlmcommerce/pdf/attestation.pdf'
					));
    			}
    
    		$this->get('mailer')->send($message);
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($entity);
    		$em->flush();
    	}
    	return $this->redirect($this->generateUrl('quote_show', array('id' => $entity->getId())));
    }
}
