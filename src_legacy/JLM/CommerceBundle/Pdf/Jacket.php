<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Pdf;

use JLM\DefaultBundle\Pdf\FPDFext;
use JLM\CommerceBundle\Model\QuoteInterface;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class Jacket extends FPDFext
{
    /**
     *
     * @var QuoteInterface $entity
     */
    private $entity;
    
    public static function get(QuoteInterface $entity)
    {
    
        $pdf = new self();
        $pdf->addPage();
        $pdf->setFont('Arial', 'B', 14);
        $pdf->cell(0, 7, $entity->getTrusteeName(), 0, 1);
        $pdf->setFont('Arial', 'I', 14);
        $pdf->multicell(0, 7, $entity->getTrusteeAddress(), 0);
        $pdf->ln(5);
        $trustee = $entity->getTrustee();
        if ($trustee) {
            $pdf->setFont('Arial', '', 14);
            foreach ($trustee->getPhones() as $phone) {
                $pdf->cell(0, 7, $phone->getLabel().' : '.$phone->getNumber(), 0, 1);
            }
            $pdf->ln(5);
        }
        $pdf->setFont('Arial', 'BI', 11);
        $pdf->cell(0, 6, $entity->getContactCp(), 0, 1);
        $contact = null;
        if ($entity->getContact()) {
            $contact = $entity->getContact()->getPerson();
        }
        if ($contact) {
            $pdf->setFont('Arial', 'I', 11);
            foreach ($contact->getPhones() as $phone) {
                $pdf->cell(0, 7, $phone->getLabel().' : '.$phone->getNumber(), 0, 1);
            }
            if ($contact->getEmail()) {
                $pdf->cell(0, 5, 'Email : '.$contact->getEmail(), 0, 1);
            }
        }
        
        $pdf->setXY(30, 100);
        $pdf->setFont('Arial', 'B', 14);
        $vise = 'Visé par :'.chr(10)
        .'     Jean-Louis'.chr(10)
        .'     Yohann'.chr(10)
        .'     Nadine'.chr(10);
        $pdf->multicell(0, 10, $vise, 0);
        $pdf->rect(30, 112, 5, 5);
        $pdf->rect(30, 122, 5, 5);
        $pdf->rect(30, 132, 5, 5);
        $pdf->setFont('Arial', 'B', 14);
        if ($entity->getDoor() !== null) {
            if ($entity->getDoor()->getCode()) {
                $pdf->setXY(100, 224);
                $pdf->cell(0, 7, $entity->getDoor()->getCode(), 0);
            }
        }
        $pdf->setXY(100, 230);
        $pdf->multicell(0, 7, $entity->getDoorCp(), 0);
        $pdf->setXY(0, 263);
        $pdf->setFont('Arial', 'B', 16);
        $pdf->cell(0, 7, $entity->getNumber(), 0);
        $pdf->setXY(0, 271);
        $pdf->setFont('Arial', 'B', 12);
        if ($entity->getDoor() !== null) {
            $city = strtoupper($entity->getDoor()->getAddress()->getCity()->getName());
            $street = $entity->getDoor()->getStreet();
        } else {
            $city = strtoupper($entity->getDoorCp());
            $street = '';
        }
        if (substr($city, 0, 5) == 'PARIS') {
            $city = trim(substr($city, 0, 6));
        }
        $pdf->cell(130, 5, $entity->getTrusteeName().'    '.$city.'    '.$street, 1);
        
        return $pdf->Output('', 'S');
    }
}
