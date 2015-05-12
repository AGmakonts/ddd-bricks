<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 12:35
 */

namespace AGmakonts\DddBricks\Entity\Exception;


class InvalidMethodCaller extends \DomainException
{

    public function __construct($message = NULL, $code = 500, \Exception $previous = NULL)
    {
        if ( NULL === $message ) {
            $message = 'This method must be called via aggregating root';
        }
        parent::__construct($message, $code, $previous);
    }
}