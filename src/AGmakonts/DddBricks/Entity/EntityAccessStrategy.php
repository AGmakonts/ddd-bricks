<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 11:47
 */

namespace AGmakonts\DddBricks\Entity;

use AGmakonts\DddBricks\Collection\CollectionInterface;
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

        $classCallerPlace = 2;

        $backTrace = debug_backtrace();

        /*
         * Check if method called directly from aggregating entity
         */
        if ( self::calledFromAggregatingRoot($backTrace, $classCallerPlace, $rootEntity) ) {
            return;
        }

        /*
         * Check if entity is not wrapped in collections, and if is go higher in backtrace
         */
        while ($classCallerPlace < count($backTrace) - 1) {
            if ( self::isWrappedInCollection($backTrace, $classCallerPlace) ) {
                continue;
            }


            if ( TRUE === ($backTrace[$classCallerPlace]['class'] === $rootEntity->value()) ) {
                return;
            }

            $classCallerPlace++;
        }

        throw new InvalidMethodCaller();
    }

    /**
     * @param $backTrace
     * @param $classCallerPlace
     *
     * @return bool
     */
    private static function isWrappedInCollection($backTrace, $classCallerPlace)
    {
        return TRUE === in_array(CollectionInterface::class, class_implements($backTrace[$classCallerPlace]['class']));
    }

    /**
     * @param $backTrace
     * @param $classCallerPlace
     * @param $rootEntity
     *
     * @return bool
     */
    private static function calledFromAggregatingRoot($backTrace, $classCallerPlace, $rootEntity)
    {
        return (TRUE === (count($backTrace) > $classCallerPlace) &&
            (TRUE === ($backTrace[$classCallerPlace]['class'] === $rootEntity->value())));
    }
}