<?php

namespace AGmakonts\DddBricks\Entity;

/**
 *
 * @author adamgrabek
 *
 */
interface EntityInterface
{
    public function assertIsTheSameAs(EntityInterface $entity);

    public function identity();
}

?>