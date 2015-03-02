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

use Symfony\Component\DependencyInjection\ContainerAware;
use JLM\ModelBundle\Entity\Mail;
use JLM\ModelBundle\Form\Type\MailType;
use JLM\CommerceBundle\JLMCommerceEvents;
use JLM\CommerceBundle\Event\QuoteEvent;
use JLM\CommerceBundle\Entity\Event;
use JLM\CommerceBundle\Entity\Quote;

/**
 * Quote controller.
 *
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class QuoteController extends ContainerAware
{
    /**
     * Lists all Quote entities.
     */
    public function indexAction()
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$states = array(
    			'all' => 'All',
    			'in_seizure' => 'InSeizure',
    			'waiting' => 'Waiting',
    			'sended' => 'Sended',
    			'given' => 'Given',
    			'canceled' => 'Canceled'
    	);
    	$state = $manager->getRequest()->get('state');
    	$state = (!array_key_exists($state, $states)) ? 'all' : $state;
    	$method = $states[$state];
    	
    	return $manager->renderResponse('JLMCommerceBundle:Quote:index.html.twig',
    			$manager->pagination('getCount'.$method, 'get'.$method, 'quote', array('state' => $state))
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
     * Nouveau devis
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
//    		var_dump($form->getData()->getEvents()[2]); exit;
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
     */
    public function printAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$entity = $manager->getEntity($id);
    	$filename = $entity->getNumber().'.pdf';
    	
    	return $manager->renderPdf($filename, 'JLMCommerceBundle:Quote:quote.pdf.php', array('entities'=>array($entity->getVariants())));
    }
    
    /**
     * Imprimer la chemise
     */
    public function jacketAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$entity = $manager->getEntity($id);
    	$filename = $entity->getNumber().'-jacket.pdf';
    	 
    	return $manager->renderPdf($filename, 'JLMCommerceBundle:Quote:jacket.pdf.php',array('entity'=>$entity));
    }
    
    /**
     * Mail
     */
    public function mailAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$entity = $manager->getEntity($id);
    	$manager->assertState($entity, array(1,2,3,4,5));
    	$mail = new Mail();
    	$mail->setSubject('Devis n°'.$entity->getNumber());
    	$mail->setFrom('commerce@jlm-entreprise.fr');
    	$mail->setBody($manager->renderView('JLMCommerceBundle:Quote:email.txt.twig', array('door' => $entity->getDoorCp())));
    	$mail->setSignature($manager->renderView('JLMCommerceBundle:QuoteVariant:emailsignature.txt.twig', array('name' => $entity->getFollowerCp())));
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
    	$form = $manager->getFormFactory()->create(new MailType(), $mail);
    	
    	return $manager->renderResponse('JLMCommerceBundle:Quote:mail.html.twig', array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	));
    }
    
    /**
     * Send by mail a QuoteVariant entity.
     */
    public function sendmailAction($id)
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$entity = $manager->getEntity($id);
    	$manager->assertState($entity, array(1,2,3,4,5));
    	$request = $manager->getRequest();
    	// Message
    	$mail = new Mail;
    	$form = $manager->getFormFactory()->create(new MailType(), $mail);
    	$form->handleRequest($request);
    	
    	if ($form->isValid())
    	{
    		$mail->setBcc('commerce@jlm-entreprise.fr');
    			
    		$message = $mail->getSwift();
    		$message->setReadReceiptTo('commerce@jlm-entreprise.fr');
    		$eventComment = 'Destinataire : '.$mail->getTo();
    		if ($mail->getCc())
    		{
    			$eventComment = chr(10).'Copie : '.$mail->getCc();
    		}
    		$eventComment .= chr(10).'Pièces jointes :';
    		foreach ($entity->getVariants() as $variant)
    		{
    			if ($variant->getState() > 0)
    			{
		    		$message->attach(\Swift_Attachment::newInstance(
		    				$manager->renderResponse('JLMCommerceBundle:Quote:quote.pdf.php',array('entities'=>array($variant))),
		    				$variant->getNumber().'.pdf','application/pdf'
		    		))
		    		;
		    		$eventComment .= chr(10).'- Devis n°'.$variant->getNumber();
		    		if ($variant->getState() < 3)
		    		{
		    			$variant->setState(3);
		    		}
    			}
    		}
    		if ($entity->getVat() == $entity->getVatTransmitter())
    		{
    			$message->attach(\Swift_Attachment::fromPath(
					$this->container->get('kernel')->getRootDir().'/../web/bundles/jlmcommerce/pdf/attestation.pdf'
				));
    			$eventComment .= chr(10).'- Attestation TVA à 10%';
    		}

    		$entity->addEvent(Quote::EVENT_SEND, $eventComment);
    		$manager->getMailer()->send($message);
    		$em = $manager->getObjectManager();
    		$em->persist($entity);
    		$em->flush();
    	}
    	
    	return $manager->redirect('quote_show', array('id' => $entity->getId()));
    }
    
    public function followerAction()
    {
    	$manager = $this->container->get('jlm_commerce.quote_manager');
    	$manager->secure('ROLE_USER');
    	$filters = array(
    			'all' => 'All',
    			'in_seizure' => 'InSeizure',
    			'waiting' => 'Waiting',
    			'sended' => 'Sended',
    			'given' => 'Given',
    			'canceled' => 'Canceled'
    	);
    	$state = $manager->getRequest()->get('state');
    	$state = (!array_key_exists($state, $filters)) ? 'all' : $state;
    	$method = $filters[$state];
    	
    	return $manager->renderResponse('JLMCommerceBundle:Quote:follower.html.twig',
    			$manager->pagination('getCount'.$method, 'get'.$method, 'quote', array('state' => $state))
    	);
    }
}
