<?php

namespace WebSK\CRUD;

use Closure;
use WebSK\Entity\InterfaceEntity;

/**
 * Class CRUDCompiler
 * @package WebSK\CRUD
 */
class CRUDCompiler
{
    /**
     * @param string|Closure $fieldname_or_closure
     * @param InterfaceEntity $obj
     * @return string|Closure
     */
    public static function fieldValueOrCallableResult($fieldname_or_closure, InterfaceEntity $obj)
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
    public static function isClosure($closure): bool
    {
        return is_object($closure) && ($closure instanceof \Closure);
    }
}
