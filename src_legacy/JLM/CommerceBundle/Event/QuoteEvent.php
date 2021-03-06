<?php

/*
 * This file is part of the JLMCommerceBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\CommerceBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use JLM\CommerceBundle\Model\QuoteInterface;
use JLM\CoreBundle\Event\RequestEvent;

class QuoteEvent extends RequestEvent
{
    /**
     * @var BillInterface
     */
    private $quote;

    /**
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(QuoteInterface $quote, Request $request)
    {
        $this->quote = $quote;
        parent::__construct($request);
    }
    
    public function getQuote()
    {
        return $this->quote;
    }
}
