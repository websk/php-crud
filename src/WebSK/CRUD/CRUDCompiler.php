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
     * @param string|Closure(InterfaceEntity): mixed $fieldname_or_closure
     * @param InterfaceEntity $obj
     * @return mixed
     */
    public static function fieldValueOrCallableResult(string|Closure $fieldname_or_closure, InterfaceEntity $obj): mixed
    {
        if ($fieldname_or_closure instanceof Closure) {
            return $fieldname_or_closure($obj);
        }

        if (CRUDFieldsAccess::objectHasProperty($obj, $fieldname_or_closure)) {
            return CRUDFieldsAccess::getObjectFieldValue($obj, $fieldname_or_closure);
        }

        return $fieldname_or_closure;
    }

    /**
     * @param mixed $closure
     */
    public static function isClosure(mixed $closure): bool
    {
        return is_object($closure) && ($closure instanceof \Closure);
    }

    public static function nullableString(mixed $value, string $description = 'Value'): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!is_scalar($value)) {
            throw new \UnexpectedValueException($description . ' must be scalar or null');
        }

        return (string) $value;
    }

    public static function string(mixed $value, string $description = 'Value'): string
    {
        return self::nullableString($value, $description) ?? '';
    }

    public static function entityId(InterfaceEntity $entity): string
    {
        $id = $entity->getId();
        if ($id === null) {
            throw new \UnexpectedValueException('Entity must be saved before its ID can be used');
        }

        return (string) $id;
    }
}
