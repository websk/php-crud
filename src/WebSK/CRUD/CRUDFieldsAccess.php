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
    /**
     * @param object $obj
     * @return mixed
     * @throws \Exception
     */
    public static function getObjId($obj)
    {
        Assert::assert($obj);

        $obj_class_name = get_class($obj);
        $obj_id_field_name = CRUDFieldsAccess::getIdFieldName($obj_class_name);
        return CRUDFieldsAccess::getObjectFieldValue($obj, $obj_id_field_name);
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

        return 'id';
    }

    /**
     * @param $obj
     * @param string $field_name
     * @return mixed
     * @throws \Exception
     * @throws \ReflectionException
     */
    public static function getObjectFieldValue($obj, string $field_name)
    {
        $obj_class_name = get_class($obj);

        $reflect = new \ReflectionClass($obj_class_name);
        $field_prop_obj = null;

        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                $field_prop_obj = $prop_obj;
            }
        }

        Assert::assert(
            $field_prop_obj,
            'Field "' . $field_name . '" not found in object. Object class: "' . $obj_class_name . '"'
        );

        $field_prop_obj->setAccessible(true);
        return $field_prop_obj->getValue($obj);
    }

    /**
     * @param InterfaceEntity $obj
     * @param array $values_arr
     * @param array $null_fields_arr список полей объекта, в которые надо внести NULL
     * @return InterfaceEntity
     * @throws \ReflectionException
     */
    public static function setObjectFieldsFromArray($obj, array $values_arr, array $null_fields_arr = [])
    {
        $reflect = new \ReflectionClass($obj);

        foreach ($values_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, $value);
        }

        foreach ($null_fields_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, null);
        }

        return $obj;
    }
}
