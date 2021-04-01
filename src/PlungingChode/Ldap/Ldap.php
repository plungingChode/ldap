<?php

namespace PlungingChode\Ldap;

use Exception;
use PlungingChode\Ldap\IFilter;

class Ldap
{
    private $host, $port, $base_dn, 
            $user, $password, $cnxn;

    public function __construct($host, $port, $base_dn, $user=null, $password=null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->base_dn = $base_dn;
        $this->user = $user;
        $this->password = $password;

        $this->cnxn = $this->connect();
    }

    /** Prepare the LDAP connection */
    private function connect()
    {
        $cnxn = @ldap_connect($this->host, $this->port);

        if (!$cnxn)
            throw new Exception('Couldn\'t create LDAP connection');

        return $cnxn;
    }

    /**
     * Convert a username from `domain\username` to `username@domain`
     * format.
     * 
     * @param string $username
     *      a username in `domain\username` format
     */
    private static function toRdn(string $username)
    {
        if (!$username || !str_contains($username, '\\')) {
            return $username;
        }

        $parts = explode('\\', $username);
        return $parts[1] . '@' . $parts[0];
    }

    /**
     * Attempt to access an LDAP directory with the provided
     * credentials.
     * 
     * @param string $username
     * @param string $password
     */
    private function bind($username, $password)
    {
        $bind = @ldap_bind($this->cnxn, self::toRdn($username), $password);

        if (!$bind)
            throw new Exception('LDAP authentication failed');

        return $bind;
    }

    /**
     * Check a user's credentials in the current LDAP
     * directory.
     */
    public function authenticate($username, $password)
    {
        try {
            return $this->bind($this->cnxn, $username, $password);
        }
        catch (Exception $e) {
            return false;
        }
    }

    /**
     * Clean up the result returned by `ldap_get_entries` by removing
     * extraneous fields.
     * 
     * @param array $searchResult
     *      the return value of a `ldap_get_entries` call
     * @param array $attributes
     *      the attributes to keep in the result. If a record
     *      doesn't have a requested attribute, it's inserted as `null`.
     * @return array 
     *      the search result in `items[ idx => record[] ]` format
     */
    public static function clean(array $searchResult, array $attributes=[])
    {
        if (count($searchResult) === 0) {
            return [];
        }

        // if no attributes are specified, select every named field
        if (count($attributes) === 0) {
            $attributes = array_keys($searchResult[0]);

            foreach ($attributes as $k => $a) {
                if (is_int($a)) {
                    unset($attributes[$k]);
                }
            }
        }

        // remove 'count'
        $cleaned = array_slice($searchResult, 1); 
        $cleaned = array_map(function ($e) use ($attributes) {
            $values = [];
        
            foreach ($attributes as $field) {
                $value = @$e[$field];

                if (is_array($value)) {
                    $value = array_slice($value, 1);

                    if (count($value) === 1) {
                        $value = $value[0];
                    }
                }

                $values[$field] = $value;
            }

            return $values;
        }, $cleaned);

        return $cleaned;
    }

    /**
     * Execute an LDAP search using the provided filter and attributes.
     * 
     * @param IFilter $f
     * @param array $attributes [optional]
     *      if the array contains elements the result will contain only those
     *      attributes
     * @param bool $clean [optional]
     *      if set to true, irrelevant fields (e.g. `count => x`, `0 => attribute`)
     *      will be removed from the result
     */
    public function search(IFilter $f, array $attributes=[], bool $clean=true)
    {
        $bind = $this->bind($this->user, $this->password);
        
        $read = @ldap_search($this->cnxn, $this->base_dn, $f->toString(), $attributes);
        if (!$read) 
            throw new Exception('LDAP search failed');

        $entr = @ldap_get_entries($this->cnxn, $read);

        return $clean 
            ? self::clean($entr, $attributes) 
            : $entr;
    }
}