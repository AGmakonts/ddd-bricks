<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 2014-10-03
 * Time: 16:21
 */

namespace AGmakonts\DddBricks\Repository\Exception;


use Exception;

class InvalidDataForEntityException extends \InvalidArgumentException
{
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->message = sprintf("Provided data (%d fields) is not compatible", count($data));
    }



} 