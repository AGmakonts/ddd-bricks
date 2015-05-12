<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 11:47
 */

namespace AGmakonts\DddBricks\Entity;

use AGmakonts\DddBricks\Entity\Exception\InvalidMethodCaller;
use AGmakonts\DddBricks\Entity\Exception\InvalidRootEntity;

/**
 * Class EntityAccessStrategy
 * @package AGmakonts\DddBricks\Entity
 */
class EntityAccessStrategy
{

    /**
     *
     * This function checks if method called in entity was called via its aggregating entity
     *
     * @param \AGmakonts\DddBricks\Entity\AggregateEntityInterface $entity
     *
     * @throws \Exception
     * @return void
     */
    static public function ensureCalledFromRoot(AggregateEntityInterface $entity)
    {
        $rootEntity = $entity->getRootEntity();

        if ( FALSE === in_array(AggregateRootInterface::class, class_implements($rootEntity->value())) ) {
            throw new InvalidRootEntity();
        }

        $backTrace = debug_backtrace();

        if ( FALSE === (count($backTrace) >= 2) || (FALSE === ($backTrace[2]['class'] === $rootEntity->value())) ) {
            throw new InvalidMethodCaller();
        }
    }
}