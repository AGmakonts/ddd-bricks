<?php

namespace AGmakonts\DddBricks\Repository;

use AGmakonts\DddBricks\Entity\EntityInterface;
use AGmakonts\DddBricks\Repository\Exception\HelperException;
use AGmakonts\DddBricks\Repository\Exception\InvalidDataForEntityException;
use AGmakonts\DddBricks\Repository\Exception\InvalidEntityException;
use AGmakonts\DddBricks\Repository\Exception\PropertyKeyExtractionException;
use AGmakonts\STL\String\String;
use ReflectionProperty;

/**
 *
 * @author AGmakonts
 *
 */
abstract class AbstractRepository
{
    /**
     * @var AbstractRepository
     */
    protected static $_repo;


    /**
     * @var \AGmakonts\STL\String\String
     */
    private $_entityType;

    /**
     * @var \SplObjectStorage
     */
    private $_helpers;


    /**
     * @param $helper
     *
     * @throws \AGmakonts\DddBricks\Repository\Exception\HelperException
     */
    protected function registerHelper(AbstractRepository $helper)
    {
        if (FALSE === $this->_helpers instanceof \SplObjectStorage) {
            $this->_helpers = new \SplObjectStorage();
        }


        if (FALSE === $this->_helpers->offsetExists($helper->getEntityType())) {
            $this->setHelper($helper);
        }
    }


    /**
     * @param \AGmakonts\STL\String\String $entityType
     *
     * @return AbstractRepository
     * @throws \AGmakonts\DddBricks\Repository\Exception\HelperException
     */
    protected function getHelperForEntityType(String $entityType)
    {
        if (FALSE === $this->_helpers->offsetExists($entityType)) {
            throw new HelperException(HelperException::HELPER_UNKNOWN);
        }

        return $this->_helpers->offsetGet($entityType);
    }

    /**
     * @param $helper
     *
     * @throws \AGmakonts\DddBricks\Repository\Exception\HelperException
     */
    private function setHelper(AbstractRepository $helper)
    {


        if (get_called_class() === $helper) {
            throw new HelperException(HelperException::HELPER_SELF_REFERENCING);
        }


        if (TRUE === $this->_helpers->offsetExists($helper->getEntityType())) {
            throw new HelperException(HelperException::HELPER_ALREADY_REQUESTED);
        }

        $this->_helpers->attach($helper->getEntityType(), $helper);
    }

    /**
     * @param array $data
     *
     * @return \AGmakonts\DddBricks\Entity\EntityInterface
     * @throws \AGmakonts\DddBricks\Repository\Exception\InvalidEntityException
     */
    final protected function getInstance(array $data)
    {
        return $this->_createInstance($data);
    }

    /**
     * Creates instance of an Entity class and fills it
     * with provided data. New instance is created without
     * calling constructor.
     *
     * @param array $data
     *
     * @return \AGmakonts\DddBricks\Entity\EntityInterface
     * @throws InvalidEntityException
     */
    private function _createInstance(array $data)
    {
        $entityClass = new \ReflectionClass($this->getEntityType()->value());

        if (FALSE === $entityClass->isSubclassOf(EntityInterface::class)) {

            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_A_ENTITY);

        } elseif (FALSE === $entityClass->isInstantiable()) {

            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_INSTANTIABLE);

        }

        /* @var $entity EntityInterface */
        $entity     = $entityClass->newInstanceWithoutConstructor();
        $properties = $entityClass->getProperties();

        $filteredData = $this->_validateAndFilterDataKeys($data, $properties);

        unset($entityClass);

        return $this->_fillEntity($entity, $properties, $filteredData);
    }

    /**
     * @return \AGmakonts\STL\String\String
     */
    final protected function getEntityType()
    {
        if (NULL === $this->_entityType) {
            $this->setEntityType();
        }

        return $this->_entityType;
    }


    /**
     *
     * This function sets name of entity which repository is for
     * You must use _setEntityType() in this function
     *
     * @return mixed
     */
    abstract protected function setEntityType();

    /**
     * @param \AGmakonts\STL\String\String $entityType
     */
    protected function _setEntityType(String $entityType)
    {
        $this->_entityType = $entityType;
    }

    /**
     * Check if data provided for the Entity is
     * correct. Data keys are checked against
     * properties of the Entity.
     *
     * @param array                $data
     * @param ReflectionProperty[] $properties
     *
     * @return array
     */
    private function _validateAndFilterDataKeys(array $data, array $properties)
    {

        $dataKeys     = array_keys($data);
        $filteredData = [];
        $propertyKeys = $this->_extractPropertyKeys($properties);

        foreach ($dataKeys as $field) {

            $keyNameVariants = [
                $field,
                "_{$field}"
            ];

            $searchResult = array_search($keyNameVariants, $propertyKeys);

            /**
             * Quick check if result is not FALSE or other value
             */
            if (FALSE === in_array($searchResult, $keyNameVariants)) {
                throw new InvalidDataForEntityException($data);
            }

            $filteredData[$searchResult] = $data[$field];

        }

        return $filteredData;
    }

    /**
     * @param ReflectionProperty[] $properties
     *
     * @return array
     */
    private function _extractPropertyKeys(array $properties)
    {
        if (TRUE === empty($properties)) {
            throw new PropertyKeyExtractionException();
        }

        $keys = [];

        foreach ($properties as $property) {
            $keys[] = $property->getName();
        }

        return $keys;


    }

    /**
     * @param EntityInterface      $entity
     * @param ReflectionProperty[] $properties
     * @param array                $data
     *
     * @return \AGmakonts\DddBricks\Entity\EntityInterface
     */
    private function _fillEntity(EntityInterface $entity, array $properties, array $data)
    {
        foreach ($properties as $property) {
            $property->setAccessible(TRUE);
            $property->setValue($entity, $data[$property->getName()]);
        }

        return $entity;
    }


    /**
     * @param array $config
     * @param array $helpers
     *
     * @return AbstractRepository
     */
    final public static function getRepository(array $config = NULL, array $helpers = NULL)
    {
        if (NULL === static::$_repo) {
            $className = get_called_class();
            static::$_repo = new $className($config);
        }

        if (NULL !== $helpers) {
            foreach ($helpers as $helper) {
                static::$_repo->registerHelper($helper);
            }
        }

        return static::$_repo;
    }

    /**
     * @param array $config
     *
     */
    abstract protected function __construct(array $config = NULL);

    /**
     * This is just for making sure that singleton pattern is preserved
     */
    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}
