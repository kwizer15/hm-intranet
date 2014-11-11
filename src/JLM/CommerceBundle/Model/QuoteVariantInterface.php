<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Model;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
interface QuoteVariantInterface
{
	/**
	 * @return int
	 */
	public function getState();
	
	/**
	 * @param QuoteInterface|null $quote
	 * @return self
	 */
	public function setQuote(QuoteInterface $quote = null);
	
	/**
	 * @param int $number
	 * @return self
	 */
	public function setVariantNumber($number);
}