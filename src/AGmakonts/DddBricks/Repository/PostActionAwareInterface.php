<?php
/**
 * @author: Radek Adamiec
 * Date: 12.05.15
 * Time: 17:07
 */

namespace AGmakonts\DddBricks\Repository;

/**
 * Interface PostActionAwareInterface
 * @package AGmakonts\DddBricks\Repository\Exception
 */
interface PostActionAwareInterface
{

    /**
     * This method will be called after getInstance method in abstract repository
     * @return void
     */
    public function afterGetInstanceAction();
}