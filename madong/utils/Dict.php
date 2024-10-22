<?php

namespace madong\utils;

use ArrayObject;

class Dict extends ArrayObject
{
    public function __construct()
    {
        /**
         * 使用对象语法参数 ArrayObject::ARRAY_AS_PROPS
         */
        parent::__construct([], ArrayObject::ARRAY_AS_PROPS);
    }

    public function put(string $key, mixed $value): void
    {
        $this[$key] = $value;
    }

    public function putAll(object|array $entries): void
    {
        foreach ($entries as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->offsetExists($key) ? $this[$key] : $default;
    }

    public function remove(string $key): void
    {
        unset($this[$key]);
    }

    public function clear(): void
    {
        $this->exchangeArray([]);
    }

    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    public function getAll(): \stdClass
    {
        $object = new \stdClass();
        foreach ($this as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }

    public function containsKey(string $key): bool
    {
        return isset($this[$key]) ?? false;
    }

    public static function of(array $entries): self
    {
        $dict = new self();
        foreach ($entries as $key => $value) {
            $dict->put($key, $value);
        }
        return $dict;
    }
}

