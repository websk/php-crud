<?php

namespace WebSK\CRUD;

use Closure;

/**
 * Class CRUDCompiler
 * @package WebSK\CRUD
 */
class CRUDCompiler
{
    /**
     * @param string|Closure $fieldname_or_closure
     * @param object $obj
     * @return mixed
     */
    public static function fieldValueOrCallableResult($fieldname_or_closure, $obj)
    {
        if (self::isClosure($fieldname_or_closure)) {
            return $fieldname_or_closure($obj);
        }

        if (CRUDFieldsAccess::objectHasProperty($obj, $fieldname_or_closure)) {
            return CRUDFieldsAccess::getObjectFieldValue($obj, $fieldname_or_closure);
        }

        return $fieldname_or_closure;
    }

    /**
     * @param string|Closure $closure
     * @return bool
     */
    public static function isClosure($closure)
    {
        return is_object($closure) && ($closure instanceof \Closure);
    }
}
