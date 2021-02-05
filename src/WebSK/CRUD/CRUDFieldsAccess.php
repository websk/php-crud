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
    const DEFAULT_ID_FIELD_NAME = 'id';

    /**
     * @param InterfaceEntity $entity_obj
     * @return null|int
     * @throws \Exception
     */
    public static function getObjId(InterfaceEntity $entity_obj): ?int
    {
        Assert::assert($entity_obj);

        $obj_class_name = get_class($entity_obj);
        $obj_id_field_name = self::getIdFieldName($obj_class_name);

        return self::getObjectFieldValue($entity_obj, $obj_id_field_name);
    }

    /**
     * @param string $entity_class_name
     * @return string
     */
    public static function getIdFieldName(string $entity_class_name): string
    {
        if (defined($entity_class_name . '::DB_ID_FIELD_NAME')) {
            return $entity_class_name::DB_ID_FIELD_NAME;
        }

        return self::DEFAULT_ID_FIELD_NAME;
    }

    /**
     * @param InterfaceEntity $obj
     * @param $field_name
     * @return bool
     * @throws \ReflectionException
     */
    public static function objectHasProperty(InterfaceEntity$obj, $field_name): bool
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
    public static function getObjectFieldValue(InterfaceEntity $entity_obj, string $field_name)
    {
        $obj_class_name = get_class($entity_obj);

        $reflect = new \ReflectionClass($obj_class_name);
        $field_prop_obj = null;

        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                $field_prop_obj = $prop_obj;
                break;
            }
        }

        Assert::assert(
            $field_prop_obj,
            'Field "' . $field_name . '" not found in object. Object class: "' . $obj_class_name . '"'
        );

        $field_prop_obj->setAccessible(true);

        if (!$field_prop_obj->isInitialized($entity_obj)) {
            return null;
        }

        return $field_prop_obj->getValue($entity_obj);
    }

    /**
     * @param InterfaceEntity $entity_obj
     * @param array $values_arr
     * @param array $null_fields_arr - список полей объекта, в которые надо внести NULL
     * @return InterfaceEntity
     * @throws \ReflectionException
     */
    public static function setObjectFieldsFromArray(
        InterfaceEntity $entity_obj,
        array $values_arr,
        array $null_fields_arr = []
    ): InterfaceEntity
    {
        $cloned_obj = clone $entity_obj;
        $reflect = new \ReflectionClass($cloned_obj);

        foreach ($values_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($cloned_obj, $value);
        }

        foreach ($null_fields_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($cloned_obj, null);
        }

        return $cloned_obj;
    }
}
