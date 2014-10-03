<?php

namespace AGmakonts\DddBricks\Repository;

use AGmakonts\DddBricks\Entity\EntityInterface;
use AGmakonts\DddBricks\Repository\Exception\InvalidDataForEntityException;
use AGmakonts\DddBricks\Repository\Exception\InvalidEntityException;
use AGmakonts\DddBricks\Repository\Exception\PropertyKeyExtractionException;
use ReflectionProperty;

/**
 *
 * @author AGmakonts
 *
 */
abstract class AbstractRepository
{
    private $_entityType;
    private $_cache;

    private function _getEntity(array $data)
    {

    }

    /**
     * @param array $data
     *
     * @return \AGmakonts\DddBricks\Entity\EntityInterface
     * @throws InvalidEntityException
     */
    private function _createInstance(array $data)
    {
        $entityClass = new \ReflectionClass($this->getEntityType());

        if(FALSE === $entityClass->isSubclassOf(EntityInterface::class)) {
            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_A_ENTITY);
        }

        if(FALSE === $entityClass->isInstantiable()) {
            throw new InvalidEntityException($this->getEntityType(), InvalidEntityException::NOT_INSTANTIABLE);
        }

        /* @var $entity EntityInterface */
        $entity = $entityClass->newInstanceWithoutConstructor();
        $properties = $entityClass->getProperties();

        $filteredData = $this->_validateAndFilterDataKeys($data, $properties);

        return $this->_fillEntity($entity, $properties, $filteredData);
    }

    public function getEntityType()
    {
        return $this->_entityType;
    }

    /**
     * @param array                $data
     * @param ReflectionProperty[] $properties
     *
     * @return array
     */
    private function _validateAndFilterDataKeys(array $data, array $properties)
    {
        if(count($data) !== count($properties)) {
            return FALSE;
        }

        $dataKeys = array_keys($data);
        $filteredData = [];
        $propertyKeys = $this->_extractPropertyKeys($properties);

        foreach($data as $field) {

            $keyNameVariants = [
                $field,
                "_{$field}"
            ];

            $searchResult = array_search($keyNameVariants, $propertyKeys);

            /**
             * Quick check if result is not FALSE or other value
             */
            if(FALSE === in_array($searchResult, $keyNameVariants)) {
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
        if(TRUE === empty($properties)) {
            throw new PropertyKeyExtractionException();
        }

        $keys = [];

        foreach($properties as $property) {
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
        foreach($properties as $property) {
            $property->setAccessible(TRUE);
            $property->setValue($entity, $data[$property->getName()]);
        }

        return $entity;
    }

}
