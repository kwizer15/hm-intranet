<?php

/*
 * This file is part of the JLMContactBundle package.
 *
 * (c) Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JLM\ContactBundle\Model;

/**
 * @author Emmanuel Bernaszuk <emmanuel.bernaszuk@kw12er.com>
 */
interface CityInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip();

    /**
     * Get zip
     *
     * @return CountryInterface
     */
    public function getCountry();

    /**
     * To string
     *
     * @return string
     */
    public function __toString();
}
