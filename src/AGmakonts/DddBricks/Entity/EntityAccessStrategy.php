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
         * Check if method called by entity itself
         */
        if( self::calledFromEntityItself($entity, $backTrace, $classCallerPlace) ){
            return;
        }

        /*
         * Check if entity is not wrapped in collections, and if is go higher in backtrace
         */
        while ($classCallerPlace < count($backTrace) - 1) {
            $classCallerPlace++;
            if ( self::wrappedInCollection($backTrace, $classCallerPlace) ) {
                continue;
            }


            if (TRUE === isset($backTrace[$classCallerPlace]['class']) && TRUE === ($backTrace[$classCallerPlace]['class'] === $rootEntity->value()) ) {
                return;
            }

        }

        throw new InvalidMethodCaller();
    }

    /**
     * @param $backTrace
     * @param $classCallerPlace
     *
     * @return bool
     */
    private static function wrappedInCollection($backTrace, $classCallerPlace)
    {
        return (TRUE === isset($backTrace[$classCallerPlace]['class']) &&
            TRUE === in_array(CollectionInterface::class, class_implements($backTrace[$classCallerPlace]['class'])));
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
            TRUE === isset($backTrace[$classCallerPlace]['class']) &&
            TRUE === ($backTrace[$classCallerPlace]['class'] === $rootEntity->value()));
    }

    /**
     * @param \AGmakonts\DddBricks\Entity\AggregateEntityInterface $entity
     * @param                                                      $backTrace
     * @param                                                      $classCallerPlace
     *
     * @return bool
     */
    private static function calledFromEntityItself(AggregateEntityInterface $entity, $backTrace, $classCallerPlace)
    {
        return (TRUE === (count($backTrace) > $classCallerPlace) &&
            TRUE === ($backTrace[$classCallerPlace]['class'] === get_class($entity)));
    }
}