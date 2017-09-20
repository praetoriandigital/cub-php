[![Build Status](https://travis-ci.org/ivelum/cub-php.svg?branch=master)](https://travis-ci.org/ivelum/cub-php)

Cub Client for PHP
=====================

Requirements
------------
PHP versions 5.3, 5.4, 5.5, 5.6, 7.0, 7.1 compiled with cURL.

Installation
------------

### Install with Composer

If you're using `Composer <http://getcomposer.org>`_ to manage dependencies,
you can add Cub Client for PHP with it:

```json

{
    "require": {
        "cub/cub": ">=0.1.0"
    }
}
```
or to get the latest version off the master branch:

```json

{
    "require": {
        "cub/cub": "dev-master"
    }
}
```

### Install source from GitHub

Install the source code:

```bash

$ git clone git://github.com/ivelum/cub-php.git
```
And include it using the autoloader:

```php

require_once '/your/libraries/path/Cub/Autoloader.php';
Cub_Autoloader::register();
```

Or, if you're using Composer:

```php

require_once 'vendor/autoload.php';
```

Usage
------------

### Sign user in

```php
Cub_Config::$api_key = '<your-secret-key>';

$user = Cub_User::login($username = '<username>', $password = '<password>');
// Now you can use user object
$first_name = $user->first_name;
$last_name = $user->last_name;
// And so on
```

### Handle webhook from Cub
```php
Cub_Config::$api_key = '<your-secret-key>';

$user = Cub_Object::fromJson($HTTP_RAW_POST_DATA);

// In this example we handle user creation/modification:
if ($object instanceof Cub_User) {

    if ($user->deleted) {
        // object was deleted, do something with it
    } else {
        // Reload user with membership and organization info
        $user.reload(array('expand' => 'membership__organization'));

        // Now $user contains the most recent information, including membership and organization info
        $organization_name = $user->membership[0]->organization->name;
        // Do something with it
    }
}
```

Check more examples in tests.
