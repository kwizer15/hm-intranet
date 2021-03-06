<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Manager;

use JLM\CoreBundle\Manager\BaseManager as Manager;
use JLM\CommerceBundle\Form\Type\BillType;
use JLM\CommerceBundle\Entity\BillLine;
use JLM\CommerceBundle\JLMCommerceEvents;
use JLM\CoreBundle\Event\FormPopulatingEvent;
use JLM\CommerceBundle\Entity\Bill;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class BillManager extends Manager
{
    /**
     * {@inheritdoc}
     */
    protected function getFormParam(string $name, array $options = []): array
    {
        switch ($name) {
            case 'new':
                return [
                    'method' => 'POST',
                    'route' => 'bill_create',
                    'params' => [],
                    'label' => 'Créer',
                    'type' => BillType::class,
                    'entity' => null,
                ];
            case 'edit':
                return [
                    'method' => 'POST',
                    'route' => 'bill_update',
                    'params' => ['id' => $options['entity']->getId()],
                    'label' => 'Modifier',
                    'type' => BillType::class,
                    'entity' => $options['entity'],
                ];
        }

        return parent::getFormParam($name, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function populateForm($form, Request $request)
    {
        // Appel des évenements de remplissage du formulaire
        $this->dispatch(JLMCommerceEvents::BILL_FORM_POPULATE, new FormPopulatingEvent($form, $request));

        // On complète avec ce qui reste vide
        $vat = $this->om->getRepository('JLMCommerceBundle:VAT')->find(1)->getRate();
        $params = [
            'creation' => new \DateTime(),
            'vat' => $vat,
            'vatTransmitter' => $vat,
            'penalty' => $this->om->getRepository('JLMCommerceBundle:PenaltyModel')->find(1) . '',
            'property' => $this->om->getRepository('JLMCommerceBundle:PropertyModel')->find(1) . '',
            'earlyPayment' => $this->om->getRepository('JLMCommerceBundle:EarlyPaymentModel')->find(1) . '',
            'maturity' => 30,
            'discount' => 0,
        ];
        foreach ($params as $key => $value) {
            $param = $form->get($key)->getData();
            if (empty($param)) {
                $form->get($key)->setData($value);
            }
        }

        // on met un ligne vide si y en a pas
        $lines = $form->get('lines')->getData();
        if (empty($lines)) {
            $form->get('lines')->setData([new BillLine()]);
        }

        return parent::populateForm($form, $request);
    }

    public function assertState(Bill $bill, $states = [])
    {
        if (!in_array($bill->getState(), $states)) {
            return $this->redirect('bill_show', ['id' => $bill->getId()]);
        }
    }
}
