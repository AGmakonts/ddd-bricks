<?php

namespace AGmakonts\DddBricks\ValueObject\Identity;

use Rhumsaa\Uuid\Uuid as UuidGenerator;
use AGmakonts\STL\String\String;
/**
 *
 * @author AGmakonts
 *
 */
abstract class AbstractUuid implements IdentityInterface
{
    /**
     *
     * @var String
     */
    private $_uuid;

    /**
     */
    function __construct($uuid)
    {
        if(FALSE === UuidGenerator::isValid($uuid)) {
            throw new \InvalidArgumentException("Provided string '{$uuid}' is not valid UUID");
        }

        $this->_uuid = new String($uuid);

    }

    /**
     * @return String
     */
    public function getValue()
    {
        return $this->_uuid;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \AGmakonts\DddBricks\ValueObject\ValueObjectInterface::__toString()
     *
     */
    public function __toString()
    {
        return $this->getValue()->getValue();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \AGmakonts\DddBricks\ValueObject\Identity\IdentityInterface::assertIsEqualTo()
     *
     */
    public function assertIsEqualTo (IdentityInterface $identity)
    {
        return ($identity instanceof AbstractUuid &&
                TRUE === $this->getValue()->assertIsEqualTo($identity->getValue()));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \AGmakonts\DddBricks\ValueObject\ValueObjectInterface::create()
     *
     */
    public function create ()
    {
        return new static(UuidGenerator::uuid4()->toString());
    }
}

?>