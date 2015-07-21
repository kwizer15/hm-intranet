<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Builder\Email;

use JLM\CommerceBundle\Pdf\Bill;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class BillBoostMailBuilder extends BillMailBuilder
{

	/**
	 * {@inheritdoc}
	 */
	public function buildSubject()
	{
		$this->setSubject('Relance facture n°'.$this->getBill()->getNumber());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function buildBody()
	{
		$this->setBody('Bonjour,'.chr(10).chr(10)
		.''
		.'Cordialement'
		);
	}
	
	public function buildPreAttachements()
	{
		$name = 'uploads/FAC'.$this->getBill()->getNumber().'.pdf';
		Bill::save(array($this->getBill()), true, $name );
		$file = new UploadedFile($name, 'FAC'.$this->getBill()->getNumber().'.pdf','application/pdf');
		$this->getMail()->addPreAttachement($file);
	}
}