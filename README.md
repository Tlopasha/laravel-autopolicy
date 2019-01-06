# Laravel automatic policy registration

[![Latest Version on Packagist](https://img.shields.io/packagist/v/musa11971/laravel-autopolicy.svg?style=flat-square)](https://packagist.org/packages/musa11971/laravel-autopolicy)
[![Quality Score](https://img.shields.io/scrutinizer/g/musa11971/laravel-autopolicy.svg?style=flat-square)](https://scrutinizer-ci.com/g/musa11971/laravel-autopolicy)
[![Total Downloads](https://img.shields.io/packagist/dt/musa11971/laravel-autopolicy.svg?style=flat-square)](https://packagist.org/packages/musa11971/laravel-autopolicy)

The `musa11971/laravel-autopolicy` package will automatically register your Laravel policies, instead of having you manually register them.  
Once the policies are "autoloaded", the package will cache the policy map to maintain application performance.

## Installation

You can install the package via composer:

``` bash
composer require musa11971/laravel-autopolicy
```

## Usage
The package will automatically enable itself once you have installed it via composer. There are no configuration options.  
  
You no longer need to register your policies manually in the `AuthServiceProvider`. All you need to do, is make sure every policy class has the following constant:  
```php
const MODEL = User::class;
```
This is required so that the package knows which model should be assigned to this policy.

Example:
```php
<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    const MODEL = User::class;

    // Policy functions go here...
}
```

## Credits

- [Musa Semou](https://github.com/musa11971)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
