<?php

namespace AGmakonts\DddBricks\ValueObject\Identity;

use AGmakonts\DddBricks\ValueObject\ValueObjectInterface;
/**
 *
 * @author AGmakonts
 *
 */
interface IdentityInterface extends ValueObjectInterface
{
    /**
     *
     * @param ValueObjectInterface $valueObject
     * @return boolean
     */
    public function assertIsEqualTo(IdentityInterface $identity);
}

?>