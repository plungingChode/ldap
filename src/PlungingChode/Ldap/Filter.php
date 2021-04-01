<?php

namespace PlungingChode\Ldap;

use PlunginChode\Ldap\Utils\IFilter;
use PlunginChode\Ldap\Utils\LogicalFilter;
use PlunginChode\Ldap\Utils\ValueFilter;

/**
 * Provides factory methods for building filters
 */
class Filter
{
    /**
     * Create a logical 'and' filter, combining other filters.
     * 
     * @param IFilter ...$parts 
     * @return IFilter
     */
    public static function and(IFilter ...$parts)
    {
        return new LogicalFilter(LogicalFilter::OP_AND, $parts);
    }

    /**
     * Create a logical 'or' filter, combining other filters.
     * 
     * @param IFilter ...$parts 
     * @return IFilter
     */
    public static function or(IFilter ...$parts)
    {
        return new LogicalFilter(LogicalFilter::OP_OR, $parts);
    }

    /**
     * Create a logical 'not' filter, negating another filter.
     * 
     * @param IFilter $f
     * @return IFilter
     */
    public static function not(IFilter $f)
    {
        return new LogicalFilter(LogicalFilter::OP_NOT, [$f]);
    }

    /**
     * Create an equality checking filter.
     * 
     * @param string $attribute
     * @param string $value
     * @return IFilter
     */
    public static function eq(string $attribute, string $value)
    {
        return new ValueFilter($attribute, $value);
    }

    /**
     * Create a filter to check if the attribute field contains
     * the provided value (done by appending a `*` character
     * to each end of the `$value`).
     * 
     * @param string $attribute
     * @param string $value
     * @return IFilter
     */
    public static function like(string $attribute, string $value)
    {
        return new ValueFilter($attribute, '*' . strval($value) . '*'); 
    }

    /**
     * Create a filter to check if the attribute field starts with
     * the provided value (done by appending a `*` character
     * to the beginning of the `$value`).
     * 
     * @param string $attribute
     * @param string $value
     * @return IFilter
     */
    public static function startsWith(string $attribute, string $value)
    {
        return new ValueFilter($attribute, strval($value) . '*'); 
    }

    /**
     * Create a filter to check if the attribute field ends with
     * the provided value (done by appending a `*` character
     * to the end of the `$value`).
     * 
     * @param string $attribute
     * @param string $value
     * @return IFilter
     */
    public static function endsWith(string $attribute, string $value)
    {
        return new ValueFilter($attribute, '*' . strval($value)); 
    }
}