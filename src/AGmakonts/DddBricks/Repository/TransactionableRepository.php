<?php
/**
 * Created by IntelliJ IDEA.
 * User: Radek Adamiec <radek@procreative.eu>
 * Date: 18.02.15
 * Time: 10:49
 */

namespace AGmakonts\DddBricks\Repository;

/**
 * Interface TransactionableRepository
 *
 * @package   AGmakonts\DddBricks\Repository
 * @author    Radek Adamiec <radek@adamiec.it>
 * @copyright 1985 - 2015 Kelleher, Helmrich and Associates, Inc.
 */
interface TransactionableRepository {

    /**
     * Begin transaction
     * @return mixed
     */
    public function beginTransaction();

    /**
     * Finish transaction and commit changes
     * @return mixed
     */
    public function commitTransaction();


    /**
     * Finish transaction and rollback changes.
     * @return mixed
     */
    public function rollbackTransaction();
    
}