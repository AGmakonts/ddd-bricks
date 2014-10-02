<?php

namespace AGmakonts\DddBricks\Repository;

/**
 *
 * @author adamgrabek
 *
 */
abstract class AbstractRepository
{
    protected $_entityType;

    public function getEntityType()
    {
        return $this->_entityType;
    }



}

?>