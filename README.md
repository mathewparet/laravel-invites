# mathewparet/LaravelInvites

A Laravel package that helps manage invitation based user registration.

## Project Status

### master
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/?branch=master) 
[![Build Status](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/build-status/master) 
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence) 


### develop
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/?branch=develop) 
[![Build Status](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/build-status/develop) 
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mathewparet/laravel-invites/badges/code-intelligence.svg?b=develop)](https://scrutinizer-ci.com/code-intelligence) 


## Introduction

This package generates invitation codes for you to use along with registration form.

Invitation codes can
* be tied to an email address.
* be available for multiple uses (got to post on FB!).
* have defined limit for the number of times it can be used.
* can have an expiry date or never.
* can have a future activation date (before which the code cannot be used).

## Installation

You can install the package using [composer](https://getcomposer.org/).

```bash
composer require mathewparet/laravelinvites
```

For Laravel 5.5 or before, you will need to add:

```php
// config/app.php
'providers' => [
    ...
    mathewparet\LaravelInvites\LaravelInvitesServiceProvider::class,
];
```

For ease of use you can define an alias too:

```php
// config/app.php
'aliases' => [
    ...
    'LaravelInvites' => mathewparet\LaravelInvites\Facades\LaravelInvites::class
];
```
Finally run the migration:

```bash
php artisan migrate
```

## Publish Configuration
You may want to publish the configuration files if you want to customize it, or if you want to change the name of the table that will be created to store the invites.

```bash
php artisan vendor:publish --provider="\mathewparet\LaravelInvites\LaravelInvitesServiceProvider" --tag=config
```

This will copy the configuration file (```config/laravelinvites.php```) to your config directory.

The default table name is ```invites```. If you wish to change it, you can change it in the above configuraion file before running the migration.

# Usage

## Generate a single onetime use invitation code

```php
LaravelInvites::generate();
```

## Generate multiple onetime use invitation code

```php
LaravelInvites::generate(10); // generates 10 different invitation codes
```

## Generate a multi use invitation code

``` php
LaravelInvites::allow(25)->generate(); // generates a single code that can be used 25 times
```

## Override expiry date defined in the config making a non-expiry code

```php
LaravelInvites::withoutExpiry()->generate();
```

_If you always plan to use invitation codes that never expire then you can make changes in the configuration file. If the ```config('laravelinvites.expiry.type')``` = ``"never"`` then you don't need to use ```withoutExpiry()``` explicitly._

## Override expiry date defined in the config with a specific expiry date

```php
$date = Carbon\Carbon::now()->addDays(5);
LaravelInvites::setExpiry($date)->generate();
// generates an invitation code that expires in 5 days
```

## Set an active / start date

```php
$date = Carbon\Carbon::now()->addDays(5);
LaravelInvites::notBefore($date)->generate();
// generates an invitation code that can be used only after 5 days
```

OR

```php
$date = Carbon\Carbon::now()->addDays(5);
LaravelInvites::validFrom($date)->generate();
// generates an invitation code that can be used only after 5 days
```

## Generate an email ID specific invitation code

```php
LaravelInvites::for('john.doe@example.com')->generate();
// This code can be used only by john.doe@example.com
```

OR

```php
LaravelInvites::generateFor('john.doe@example.com');
```

## Check whether an invitation code is valid

```php
LaravelInvites::isValid($code);
```

OR

```php
LaravelInvites::isValid($code, $email);
```
> This method returns ```true``` or ```false```.

> If an invitation code tied to a sepcific email is checked without the correct email ID, it would return false.

> If invitation code is not tied to an email ID, and the invitation code is active and correct, this method will return true whether email ID is null not.


## Check validity of invitation code with exceptions

```php
LaravelInvites::check($code, $email);
```

OR

```php
LaravelInvites::check($code);
```

> This works just like ```isValid()``` but instead it throws an exception if validation fails.

## Redeem invitation code

```php
LaravelInvites::redeem($code, $email);
``` 
OR
```php
LaravelInvites::redeem($code);
```

> ```isValid()``` should be called before invoking this since it will throw an exception if invitation code is invalid.

## Get list of invitation codes

```php
LaravelInvites::get();
```

OR

```php
LaravelInvites::for('john.doe@example.com')->get();
```

## Form request validation

To validate an invitation code submitted via form:

```php
public function store(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users',
        'code' => 'required|valid_code:email', // here email is the field that holds the email id
    ]);

    // Add the user to the database.
}
```

# Console

The below console commands are available. 

```bash
php artisan invites:generate
```
```bash
php artisan invites:cleanup
```
```bash
php artisan invites:check
```

# Email with Invitation Code
When invitation code is generated for a specific email ID (using ```php artisan invites:generate``` or using the Facade), an invitation mail will be automatically sent to the email ID.

You can disable, customize this in the configuraiton file. 

If it is enabled, then when a user clicks on the invitation link, his email ID and invitation code will be automatically filled in your registration form. It will work out of the box if you use the built in registration form that comes with Laravel. Else you can customize the route name for the registration form under ```routes.register```. You can customize ```email``` and ```code``` fields names as in your registration form so that it can be auto populated.

You can publish the mail mail markdown if you wish to customize it:

```bash
php artisan vendor:publish --provider="mathewparet\LaravelInvites\LaravelInvitesServiceProvider" --tag=mail