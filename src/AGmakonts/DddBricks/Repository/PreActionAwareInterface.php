<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 17:07
 */

namespace AGmakonts\DddBricks\Repository;

/**
 * Interface PreActionAwareInterface
 * @package AGmakonts\DddBricks\Repository\Exception
 */
interface PreActionAwareInterface
{

    /**
     * This method will be called before getInstance in abstract repository
     * @return void
     */
    public function beforeGetInstanceAction();
}