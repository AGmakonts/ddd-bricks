<?php
/**
 * Created by PhpStorm.
 * User: adamgrabek
 * Date: 07/10/14
 * Time: 01:34
 */

namespace AGmakonts\DddBricks\Repository\Exception;


use Exception;

class HelperException extends \Exception
{
    const HELPER_UNKNOWN           = 'Requested helper does not exists!';
    const HELPER_INVALID           = 'Requested helper is not Repository!';
    const HELPER_ALREADY_REQUESTED = 'Requested helper is already added to the specification';
    const HELPER_SELF_REFERENCING  = 'Helper cannot be requested from itself!';

    public function __construct($error)
    {
        $this->message = $error;
    }


} 