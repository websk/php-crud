<?php

namespace WebSK\CRUD;

use WebSK\Utils\Assert;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDFieldsAccess
 * @package WebSK\CRUD
 */
class CRUDFieldsAccess
{
    const string DEFAULT_ID_FIELD_NAME = 'id';

    /**
     * @param InterfaceEntity $entity_obj
     * @return null|int
     * @throws \Exception
     */
    public static function getObjId(InterfaceEntity $entity_obj): ?int
    {
        return $entity_obj->getId();
    }

    /**
     * @param string $entity_class_name
     * @return string
     */
    /** @param class-string $entity_class_name */
    public static function getIdFieldName(string $entity_class_name): string
    {
        $constant_name = $entity_class_name . '::DB_ID_FIELD_NAME';
        if (defined($constant_name)) {
            $value = constant($constant_name);
            if (!is_string($value) || $value === '') {
                throw new \UnexpectedValueException($constant_name . ' must be a non-empty string.');
            }

            return $value;
        }

        return self::DEFAULT_ID_FIELD_NAME;
    }

    /**
     * @param InterfaceEntity $obj
     * @param $field_name
     * @return bool
     * @throws \ReflectionException
     */
    public static function objectHasProperty(InterfaceEntity $obj, string $field_name): bool
    {
        $obj_class_name = get_class($obj);
        $reflect = new \ReflectionClass($obj_class_name);
        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param string $field_name
     * @return mixed
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getObjectFieldValue(InterfaceEntity $entity_obj, string $field_name): mixed
    {
        $obj_class_name = get_class($entity_obj);

        $reflect = new \ReflectionClass($obj_class_name);
        if (!$reflect->hasProperty($field_name)) {
            throw new \UnexpectedValueException(
                'Field "' . $field_name . '" not found in object. Object class: "' . $obj_class_name . '"'
            );
        }
        $field_prop_obj = $reflect->getProperty($field_name);

        if (!$field_prop_obj->isInitialized($entity_obj)) {
            return null;
        }

        return $field_prop_obj->getValue($entity_obj);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param array<string, mixed> $values_arr
     * @param list<string> $null_fields_arr - список полей объекта, в которые надо внести NULL
     * @throws \ReflectionException
     */
    public static function setObjectFieldsFromArray(
        InterfaceEntity $entity_obj,
        array $values_arr,
        array $null_fields_arr = []
    ): void
    {
        $reflect = new \ReflectionClass($entity_obj);

        foreach ($values_arr as $field_name => $value) {
            $property_obj = $reflect->getProperty($field_name);

            $property_type = $property_obj->getType();
            if ($property_type instanceof \ReflectionNamedType && $property_type->isBuiltin() && $value !== null) {
                settype($value, $property_type->getName());
            }
            $property_obj->setValue($entity_obj, $value);
        }

        foreach ($null_fields_arr as $field_name) {
            $property_obj = $reflect->getProperty($field_name);
            $property_obj->setValue($entity_obj, null);
        }
    }
}
