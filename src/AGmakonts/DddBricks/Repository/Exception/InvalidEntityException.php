<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 2014-10-03
 * Time: 16:00
 */

namespace AGmakonts\DddBricks\Repository\Exception;


class InvalidEntityException extends \Exception
{
    const NOT_A_ENTITY     = "Class of type '%s' is not valid entity";
    const NOT_INSTANTIABLE = "Entity must  be instantiable and not an Interface or Abstract class";


    /**
     * @param string $type
     * @param string $error
     */
    public function __construct($type, $error)
    {
        $this->message = sprintf($error, $type);
    }
}