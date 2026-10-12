<?php

namespace WebSK\CRUD;

use Psr\Http\Message\ServerRequestInterface;

final class RequestParams
{
    public static function query(ServerRequestInterface $request, string $key, mixed $default = null): mixed
    {
        $query_params = $request->getQueryParams();

        return $query_params[$key] ?? $default;
    }

    public static function parsedBody(ServerRequestInterface $request, string $key, mixed $default = null): mixed
    {
        $parsed_body = $request->getParsedBody();

        if (is_array($parsed_body)) {
            return $parsed_body[$key] ?? $default;
        }

        if (is_object($parsed_body) && property_exists($parsed_body, $key)) {
            return $parsed_body->{$key};
        }

        return $default;
    }

    public static function parsedString(
        ServerRequestInterface $request,
        string $key,
        ?string $default = null
    ): string {
        $value = self::parsedBody($request, $key, $default);
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Request field "' . $key . '" must be a string');
        }

        return $value;
    }

    public static function parsedInt(ServerRequestInterface $request, string $key): int
    {
        $value = self::parsedBody($request, $key);
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        throw new \InvalidArgumentException('Request field "' . $key . '" must be an integer');
    }

    public static function queryString(
        ServerRequestInterface $request,
        string $key,
        string $default = ''
    ): string {
        $value = self::query($request, $key, $default);
        if (!is_scalar($value) && $value !== null) {
            throw new \InvalidArgumentException('Query parameter "' . $key . '" must be scalar');
        }

        return $value === null ? $default : (string) $value;
    }

    /** @return list<string> */
    public static function queryStringList(ServerRequestInterface $request, string $key): array
    {
        $value = self::query($request, $key, []);
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Query parameter "' . $key . '" must be an array');
        }

        $result = [];
        foreach ($value as $item) {
            if (!is_scalar($item) && $item !== null) {
                throw new \InvalidArgumentException('Query parameter "' . $key . '" contains a non-scalar value');
            }
            $result[] = (string) $item;
        }

        return $result;
    }
}
