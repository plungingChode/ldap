# plunging-chode/ldap

Install with [composer](https://getcomposer.org/download/):  
```
composer require plunging-chode/ldap
```

Make an LDAP query or validate a user's credentials:
```php
use PlungingChode\Ldap\Ldap;
use PlungingChode\Ldap\Filter;

$ldap = new Ldap(
        // Supply a host and port
        'ldap.host', 386,
        // Base DN used for searches
        'dc=example,dc=local',
        // A user with search privileges
        'user@example', 'user_pw'
    );

// Try to login as `usr`. Returns true or false
$correctCredentials = $ldap->authenticate('usr', 'password');

// Specify search fields
$lookFor = ['sn', 'givenname', 'mail'];

// Build a query string
$filter = Filter::and(
    Filter::eq('sn', 'Jameson'),
    Filter::or(
        Filter::eq('givenname', 'James'),
        Filter::eq('givenname', 'Jonah')
    )
)

// Execute query and fetch results as an array
$results = $ldap->search($filter, $lookFor);

// Check results
echo '<pre>' . var_export($results, true) . '</pre>';
```
