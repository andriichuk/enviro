<?php

declare(strict_types=1);

namespace Andriichuk\KeepEnv\Specification;

use Andriichuk\KeepEnv\Contracts\ArraySerializable;
use InvalidArgumentException;

/**
 * @psalm-immutable
 */
class Variable implements ArraySerializable
{
    public string $name;
    public string $description;
    public bool $export;
    public array $rules;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @param mixed $default
     */
    public function __construct(string $name, string $description, bool $export = false, array $rules = [], $default = null)
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Variable name cannot be empty');
        }

        $this->name = $name;
        $this->description = $description;
        $this->export = $export;
        $this->rules = $rules;
        $this->default = $default;
    }

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'export' => $this->export ?: null,
            'rules' => $this->rules ?: null,
            'default' => $this->default,
        ]);
    }
}
