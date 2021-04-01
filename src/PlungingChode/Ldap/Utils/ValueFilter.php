<?php

namespace PlungingChode\Ldap\Utils;

/**
 * Represents a `key = value` filter.
 */
class ValueFilter implements IFilter
{
    protected $field;
    protected $value;

    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function toString()
    {
        return '(' . strval($this->field) . '=' . strval($this->value) . ')';
    }
}