<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 11:46
 */

namespace AGmakonts\DddBricks\Entity;


/**
 * Class AggregateEntityInterface
 * @package AGmakonts\DddBricks\Entity
 */
interface AggregateEntityInterface extends EntityInterface
{

    /**
     * Get entity name that aggregates this entity
     *
     * @return \AGmakonts\STL\String\String
     */
    public function getRootEntity();
}