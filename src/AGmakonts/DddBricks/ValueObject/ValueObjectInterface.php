<?php

namespace AGmakonts\DddBricks\ValueObject;

/**
 *
 * @author adamgrabek
 *
 */
interface ValueObjectInterface
{
    /**
     * @return ValueObjectInterface
     */
    public static function create();

    /**
     *
     * @param ValueObjectInterface $valueObject
     * @return boolean
     */
    public function assertIsEqualTo(ValueObjectInterface $valueObject);


    /**
     * @return mixed
     */
    public function __toString();
}

?>