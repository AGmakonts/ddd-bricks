<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 2014-10-03
 * Time: 16:54
 */

namespace AGmakonts\DddBricks\Repository\Exception;


use Exception;

class PropertyKeyExtractionException extends \InvalidArgumentException
{
    public function __construct()
    {
        $this->message = "Property list is invalid";
    }

} 