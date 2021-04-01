<?php

namespace PlungingChode\Ldap\Utils;

/**
 * Represents a logical filter, composed of other logical-
 * and value filters.
 */
class LogicalFilter implements IFilter
{
    const OP_AND = '&';
    const OP_OR  = '|';
    const OP_NOT = '!';

    protected $method;
    protected array $to_combine;

    public function __construct($method, $parts)
    {
        $this->method = $method;
        $this->to_combine = $parts;
    }

    public function toString()
    {
        $str = '(' . $this->method;
        foreach ($this->to_combine as $filter) {
            $str .= $filter->toString();
        }
        $str .= ')';
        return $str;
    }
}
