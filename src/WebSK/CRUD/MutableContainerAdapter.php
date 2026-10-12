<?php

namespace WebSK\CRUD;

use DI\Container;
use Psr\Container\ContainerInterface;

/**
 * Adapts PHP-DI 7's mutable API to legacy WebSK service providers that register
 * definitions through ArrayAccess.
 *
 * @implements \ArrayAccess<array-key, mixed>
 */
final class MutableContainerAdapter implements ContainerInterface, \ArrayAccess
{
    public function __construct(private readonly Container $container)
    {
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->container->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Container identifier must be a string.');
        }

        return $this->container->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($offset) || $offset === '') {
            throw new \InvalidArgumentException('Container identifier must be a non-empty string.');
        }

        $this->container->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('PHP-DI definitions cannot be removed at runtime.');
    }
}
