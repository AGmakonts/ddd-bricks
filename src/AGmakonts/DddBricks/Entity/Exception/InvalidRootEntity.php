<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 11:52
 */

namespace AGmakonts\DddBricks\Entity\Exception;

/**
 * Class InvalidRootEntity
 * @package AGmakonts\DddBricks\Entity\Exception
 */
class InvalidRootEntity extends \DomainException
{
    public function __construct($message = NULL, $code = 500, \Exception $previous = NULL)
    {
        if ( NULL === $message ) {
            $message = 'Aggregating entity must be instance of AggregateRootInterface';
        }
        parent::__construct($message, $code, $previous);
    }
}