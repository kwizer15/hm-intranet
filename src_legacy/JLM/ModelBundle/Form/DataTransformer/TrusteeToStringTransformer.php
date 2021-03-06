<?php

namespace JLM\ModelBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use JLM\ModelBundle\Entity\City;

class TrusteeToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (trustee) to a string (name).
     *
     * @param  Trustee|null $entity
     *
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return "";
        }
        return $entity . '';
    }

    /**
     * Transforms a string (number) to an object (trustee).
     *
     * @param  string $number
     *
     * @return Trustee|null
     * @throws TransformationFailedException if object (trustee) is not found.
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }


        $entity = $this->om
            ->getRepository('JLMModelBundle:Trustee')
            ->findOneBy(['name' => trim($string)])
        ;
        if (null === $entity) {
            throw new TransformationFailedException(
                sprintf(
                    'A trustee with name "%s" does not exist!',
                    $string
                )
            );
        }

        return $entity;
    }
}
