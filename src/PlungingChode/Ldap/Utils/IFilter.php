<?php

namespace PlungingChode\Ldap\Utils;

interface IFilter 
{
    /** Converts this object into a valid LDAP filter string. */
    public function toString();
}
