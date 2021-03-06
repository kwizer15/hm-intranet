<?php

namespace JLM\OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JLM\DailyBundle\Entity\Intervention;

/**
 * Actions controller.
 *
 * @Route("/tocontact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/", name="tocontact")
     * @Template()
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_OFFICE');

        $em = $this->getDoctrine()->getManager();
        $list = $em->getRepository('JLMDailyBundle:Intervention')->getToContact();
        return ['entities' => $list];
    }
    
    /**
     * @Route("/tocontact/contacted/{id}", name="tocontact_contacted")
     */
    public function contactedAction(Intervention $entity)
    {
        $this->denyAccessUnlessGranted('ROLE_OFFICE');

        $em = $this->getDoctrine()->getManager();
        $entity->setContactCustomer(true);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('tocontact'));
    }
}
