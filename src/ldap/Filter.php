<?php

namespace ldap;

interface IFilter {
    public function toString();
}

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

class ValueFilter implements IFilter
{
    protected $field;
    protected $value;

    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public static function eq($field, $value)
    {
        return new ValueFilter($field, $value);
    }

    public function toString()
    {
        return '(' . strval($this->field) . '=' . strval($this->value) . ')';
    }
}

class Filter
{
    public static function and(IFilter ...$parts)
    {
        return new LogicalFilter(LogicalFilter::OP_AND, $parts);
    }

    public static function or(IFilter ...$parts)
    {
        return new LogicalFilter(LogicalFilter::OP_OR, $parts);
    }

    public static function not(IFilter ...$parts)
    {
        return new LogicalFilter(LogicalFilter::OP_NOT, $parts);
    }

    public static function eq($field, $value)
    {
        return new ValueFilter($field, $value);
    }

    public static function like($field, $value)
    {
        return new ValueFilter($field, '*' . strval($value) . '*'); 
    }

    public static function startsWith($field, $value)
    {
        return new ValueFilter($field, strval($value) . '*'); 
    }

    public static function endsWith($field, $value)
    {
        return new ValueFilter($field, '*' . strval($value)); 
    }
}