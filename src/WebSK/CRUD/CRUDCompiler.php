<?php

namespace WebSK\CRUD;

/**
 * Class CRUDCompiler
 * @package WebSK\CRUD
 */
class CRUDCompiler
{
    /**
     * @param $fieldname_or_callable
     * @param $obj
     * @return mixed
     */
    public static function fieldValueOrCallableResult($fieldname_or_callable, $obj)
    {
        if (self::isClosure($fieldname_or_callable)) {
            return $fieldname_or_callable($obj);
        }

        if (CRUDFieldsAccess::objectHasProperty($obj, $fieldname_or_callable)) {
            return CRUDFieldsAccess::getObjectFieldValue($obj, $fieldname_or_callable);
        }

        return $fieldname_or_callable;
    }

    public static function isClosure($t)
    {
        return is_object($t) && ($t instanceof \Closure);
    }
}
