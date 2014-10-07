<?php

namespace AGmakonts\DddBricks\Repository;

use AGmakonts\DddBricks\Entity\EntityInterface;
use AGmakonts\DddBricks\Repository\Exception\HelperException;
use AGmakonts\DddBricks\Repository\Exception\InvalidDataForEntityException;
use AGmakonts\DddBricks\Repository\Exception\InvalidEntityException;
use AGmakonts\DddBricks\Repository\Exception\PropertyKeyExtractionException;
use ReflectionProperty;
use Symfony\Component\Console\Helper\Helper;

/**
 *
 * @author AGmakonts
 *
 */
abstract class AbstractRepository
{
    private $_entityType;
    private $_requiredHelpers;

    protected function requestHelpers(array $helpers)
    {
        foreach($helpers as $helper) {
            $this->requestHelper($helper);
        }
    }

    /**
     * @param $helper
     *
     * @throws \AGmakonts\DddBricks\Repository\Exception\HelperException
     */
    protected function requestHelper($helper)
    {
        if(get_called_class() === $helper) {
            throw new HelperException(HelperException::HELPER_SELF_REFERENCING);
        }

        if(TRUE === in_array($helper, $this->_requiredHelpers)) {
            throw new HelperException(HelperException::HELPER_ALREADY_REQUESTED);
        }

        if(FALSE === class_exists($helper)) {
            throw new HelperException(HelperException::HELPER_UNKNOWN);
        }

        $helperClass = new \ReflectionClass($helper);

        if(FALSE === $helperClass->isSubclassOf(self::class)) {
            throw new HelperException(HelperException::HELPER_INVALID);
        }


        $this->_requiredHelpers[$helper] = NULL;

        return $this;



    }


    private function _getEntity(array $data)
    {

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
        $entityClass = new \ReflectionClass($this->getEntityType());

        if (FALSE === $entityClass->isSubclassOf(EntityInterface::class)) {

            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_A_ENTITY);

        } elseif (FALSE === $entityClass->isInstantiable()) {

            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_INSTANTIABLE);

        }

        /* @var $entity EntityInterface */
        $entity = $entityClass->newInstanceWithoutConstructor();
        $properties = $entityClass->getProperties();

        $filteredData = $this->_validateAndFilterDataKeys($data, $properties);

        unset($entityClass);

        return $this->_fillEntity($entity, $properties, $filteredData);
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return $this->_entityType;
    }

    /**
     * Check if data provided for the Entity is
     * correct. Data keys are checked against
     * properties of the Entity.
     *
     * @param array $data
     * @param ReflectionProperty[] $properties
     *
     * @return array
     */
    private function _validateAndFilterDataKeys(array $data, array $properties)
    {
        if (count($data) !== count($properties)) {
            return FALSE;
        }

        $dataKeys = array_keys($data);
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
     * @param EntityInterface $entity
     * @param ReflectionProperty[] $properties
     * @param array $data
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

}
