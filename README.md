# PHP JSON Pointer

[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-pointer/v/stable)](https://packagist.org/packages/remorhaz/php-json-pointer)
[![License](https://poser.pugx.org/remorhaz/php-json-pointer/license)](https://packagist.org/packages/remorhaz/php-json-pointer)
[![Build Status](https://travis-ci.org/remorhaz/php-json-pointer.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-pointer)
[![Code Climate](https://codeclimate.com/github/remorhaz/php-json-pointer/badges/gpa.svg)](https://codeclimate.com/github/remorhaz/php-json-pointer)
[![Test Coverage](https://codeclimate.com/github/remorhaz/php-json-pointer/badges/coverage.svg)](https://codeclimate.com/github/remorhaz/php-json-pointer/coverage)

This library implements [RFC6901](https://tools.ietf.org/html/rfc6901)-compliant JSON pointers.

##Requirements
* PHP 7.0+

##Features
* Supports PHP 7.0
* No PHP extensions required
* Throws SPL exceptions

#License
PHP JSON Pointer is licensed under MIT license.

#Installation
You will need [composer](https://getcomposer.org) to perform install.
```
composer require remorhaz/php-json-pointer
```

#Documentation
## Data accessors
Pointer utilizes JSON data accessor interfaces defined in package
**[remorhaz/php-json-data](https://github.com/remorhaz/php-json-data)**. Read more about them in package documentation.
There is a ready-to-work implementation in that package that works with native PHP structures (like the ones you get as
a result of `json_decode` function). You can use `RawSelectableReader` class for read-only operations like _test_ and
_read_, or `RawSelectableWriter` class for all operations. You can also implement your own accessors if you need to work
with another sort of data (like unparsed JSON text, for example).

## Using pointer
To access data with JSON Pointer links you need just 3 simple steps:

1. Create an instance of accessor bound to your data.
2. Create an object of `\Remorhaz\JSONPointer\Pointer` by calling it's constructor with an accessor as an argument.
3. Call one of operation methods: `test()`, `read()`, `add()`, `replace()` or `remove()`. You will also need an instance
of accessor bound to source data for `add()` and `replace()` operations.

##Example of usage
```php
<?php

use \Remorhaz\JSON\Data\RawSelectableReader;
use \Remorhaz\JSON\Data\RawSelectableWriter;
use \Remorhaz\JSON\Pointer\Pointer;

// Setting up data.
$data = (object) [
    'a' => (object) [
        'b' => ['c', 'd', 'e'],
    ],
];

// Setting up read-only pointer.
$reader = new RawSelectableReader($data);
$readPointer = new Pointer($reader);

// Reading value.
echo $readPointer->read("/a/b/1")->getData();  // d

// Testing value.
$trueResult = $readPointer->test("/a/b/1"); // Sets $trueResult to TRUE (value exists).
$falseResult = $readPointer->test("/a/c"); // Sets $falseResalt to FALSE (value doesn't exist).

// Setting up writable pointer.
$writer = new RawSelectableWriter($data);
$writePointer = new Pointer($writer);

// Appending element to array.
$value = 'f';
$valueReader = new RawSelectableReader($value);
$writePointer->replace("/a/b/1", $valueReader); // Sets $data->a->b to ['c', 'f', 'e'].
$value = 'g';
$valueReader = new RawSelectableReader($value);
$writePointer->add("/a/c", $valueReader); // Adds $data->a->c property and sets it to 'g'.
$writePointer->remove("/a/b/0"); // Sets $data->a->b to ['f', 'e'].
$value = 'h';
$valueReader = new RawSelectableReader($value);
$writePointer->add("/a/b/-", $valueReader); // Sets $data->a->b to ['f', 'e', 'h'].
```
