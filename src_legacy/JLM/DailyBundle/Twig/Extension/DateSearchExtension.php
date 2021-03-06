<?php

/*
 * This file is part of the JLMDailyBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\DailyBundle\Twig\Extension;

use JLM\ModelBundle\Form\Type\DatepickerType;
use Symfony\Component\Form\FormFactory;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
class DateSearchExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $formService;

    public function __construct(FormFactory $formService)
    {
        $this->formService = $formService;
    }

    public function getName()
    {
        return 'date_search_extension';
    }

    public function getGlobals()
    {
        $form = $this->formService->create(DatepickerType::class, new \DateTime());
        return [
            'date_search' => [
                'form' => $form->createView(),
            ],
        ];
    }
}
