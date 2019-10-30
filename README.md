# PHP JSON Pointer

[![Latest Stable Version](https://poser.pugx.org/remorhaz/php-json-pointer/v/stable)](https://packagist.org/packages/remorhaz/php-json-pointer)
[![Build Status](https://travis-ci.org/remorhaz/php-json-pointer.svg?branch=master)](https://travis-ci.org/remorhaz/php-json-pointer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/remorhaz/php-json-pointer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/remorhaz/php-json-pointer/?branch=master)
[![codecov](https://codecov.io/gh/remorhaz/php-json-pointer/branch/master/graph/badge.svg)](https://codecov.io/gh/remorhaz/php-json-pointer)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/remorhaz/php-json-pointer/master)](https://infection.github.io)
[![Total Downloads](https://poser.pugx.org/remorhaz/php-json-pointer/downloads)](https://packagist.org/packages/remorhaz/php-json-pointer)
[![License](https://poser.pugx.org/remorhaz/php-json-pointer/license)](https://packagist.org/packages/remorhaz/php-json-pointer)

This library implements [RFC6901](https://tools.ietf.org/html/rfc6901)-compliant JSON pointers.

## Requirements
* PHP 7.3+

## Features
* Selecting part of a JSON document.
* Removing part of a JSON document.
* Replacing/adding part of a JSON document.

# Installation
You will need [composer](https://getcomposer.org) to perform install.
```
composer require remorhaz/php-json-pointer
```

# Documentation
## Accessing JSON document
You can create accessible JSON document either from encoded JSON string or from decoded JSON data using corresponding _node value factory_:
```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson;
use Remorhaz\JSON\Data\Value\DecodedJson;

// Creating document from JSON-encoded string:
$encodedValueFactory = EncodedJson\NodeValueFactory::create();
$encodedJson = '{"a":1}';
$document1 = $encodedValueFactory->createValue($encodedJson);

// Creating document from decoded JSON data:
$decodedValueFactory = DecodedJson\NodeValueFactory::create();
$decodedJson = (object) ['a' => 1];
$document2 = $decodedValueFactory->createValue($decodedJson);
``` 

## Creating query
You should use _query factory_ to create query from JSON Pointer expression:
```php
<?php
use Remorhaz\JSON\Pointer\Query\QueryFactory;

$queryFactory = QueryFactory::create();

// Creating query that selects 'a' property from document:
$query = $queryFactory->createQuery('/a');
```
_Selectable_ query is a query that doesn't contain `-` reference.

## Processing query
You should use an instance of _query processor_ to execute queries on given JSON documents:
```php
<?php
use Remorhaz\JSON\Pointer\Processor\Processor;

$processor = Processor::create();
```

### Selecting part of a JSON document
You can get part of a JSON document using _selectable_ query and `::select()` method.

```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

$nodeValueFactory = NodeValueFactory::create();
$queryFactory = QueryFactory::create();
$processor = Processor::create();

$document = $nodeValueFactory->createValue('{"a":"b"}');

// Selecting existing value
$query1 = $queryFactory->createQuery('/a');
$result1 = $processor->select($query1, $document);
var_dump($result1->exists()); // boolean: true
var_dump($result1->decode()); // string: 'b'
var_dump($result1->encode()); // string: '"b"'

// Attempting to select non-existing value
$query2 = $queryFactory->createQuery('/c');
$result2 = $processor->select($query2, $document);
var_dump($result2->exists()); // boolean: false
var_dump($result2->decode()); // throws an exception

// Attempting to use non-selectable query
$query3 = $queryFactory->createQuery('/-');
$result3 = $processor->select($query3, $document); // throws an exception
``` 
Note that you can either encode result of a selection to JSON string or decode them to raw PHP data. Before accessing a result of `::select()` you can check it's existence with `::exists()` method to avoid exception.

### Deleting part of a JSON document
You can delete part of a JSON document using _selectable_ query and `::delete()` method.
```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

$nodeValueFactory = NodeValueFactory::create();
$queryFactory = QueryFactory::create();
$processor = Processor::create();

$document = $nodeValueFactory->createValue('{"a":"b","c":"d"}');

// Deleting existing value
$query1 = $queryFactory->createQuery('/a');
$result1 = $processor->delete($query1, $document);
var_dump($result1->exists()); // boolean: true
var_dump($result1->encode()); // string: '{"c":"d"}'

// Attempting to delete non-existing value
$query2 = $queryFactory->createQuery('/e');
$result2 = $processor->delete($query2, $document);
var_dump($result2->exists()); // boolean: false
var_dump($result2->encode()); // throws an exception

// Attempting to use non-selectable query
$query3 = $queryFactory->createQuery('/-');
$result3 = $processor->delete($query3, $document); // throws an exception
```
Note that `::delete()` function returns non-existing result if query points to non-existing value.

### Replacing part of a JSON document
You can replace part of a JSON document using _selectable_ query and `::replace()` method.
```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

$nodeValueFactory = NodeValueFactory::create();
$queryFactory = QueryFactory::create();
$processor = Processor::create();

$document = $nodeValueFactory->createValue('{"a":"b","c":"d"}');
$replacement = $nodeValueFactory->createValue('"e"');

// Replacing existing value
$query1 = $queryFactory->createQuery('/a');
$result1 = $processor->replace($query1, $document, $replacement);
var_dump($result1->exists()); // boolean: true
var_dump($result1->encode()); // string: '{"a":"e","c":"d"}'

// Attempting to replace non-existing value
$query2 = $queryFactory->createQuery('/f');
$result2 = $processor->replace($query2, $document, $replacement);
var_dump($result2->exists()); // boolean: false
var_dump($result2->encode()); // throws an exception

// Attempting to use non-selectable query
$query3 = $queryFactory->createQuery('/-');
$result3 = $processor->replace($query3, $document, $replacement); // throws an exception
```

### Adding part of a JSON document
You can add part of a JSON document using _any_ query and `::add()` method.
```php
<?php
use Remorhaz\JSON\Data\Value\EncodedJson\NodeValueFactory;
use Remorhaz\JSON\Pointer\Processor\Processor;
use Remorhaz\JSON\Pointer\Query\QueryFactory;

$nodeValueFactory = NodeValueFactory::create();
$queryFactory = QueryFactory::create();
$processor = Processor::create();

// Working with object
$document1 = $nodeValueFactory->createValue('{"a":"b","c":"d"}');
$replacement1 = $nodeValueFactory->createValue('"e"');

// Replacing existing property
$query1 = $queryFactory->createQuery('/a');
$result1 = $processor->add($query1, $document1, $replacement1);
var_dump($result1->exists()); // boolean: true
var_dump($result1->encode()); // string: '{"a":"e","c":"d"}'

// Adding non-existing property
$query2 = $queryFactory->createQuery('/f');
$result2 = $processor->add($query2, $document1, $replacement1);
var_dump($result2->exists()); // boolean: true
var_dump($result2->encode()); // string: '{"a":"b","c":"d","f":"e"}'

// Adding non-existing property
$query2 = $queryFactory->createQuery('/f');
$result2 = $processor->add($query2, $document1, $replacement1);
var_dump($result2->exists()); // boolean: true
var_dump($result2->encode()); // string: '{"a":"b","c":"d","f":"e"}'

// Attempting to add property to non-existing object
$query3 = $queryFactory->createQuery('/e/f');
$result3 = $processor->add($query3, $document1, $replacement1);
var_dump($result3->exists()); // boolean: false
var_dump($result3->encode()); // throws an exception

// Working with array
$document2 = $nodeValueFactory->createValue('[1,2]');
$replacement2 = $nodeValueFactory->createValue('3');

// Inserting new element before given index
$query4 = $queryFactory->createQuery('/1');
$result4 = $processor->add($query4, $document2, $replacement2);
var_dump($result4->exists()); // boolean: true
var_dump($result4->encode()); // string: '[1,3,2]'

// Appending new element to the end of array
$query5 = $queryFactory->createQuery('/-');
$result5 = $processor->add($query5, $document2, $replacement2);
var_dump($result5->exists()); // boolean: true
var_dump($result5->encode()); // string: '[1,2,3]'
```
If query points to existing property it will be replaced. Note that you can add values only to existing objects/arrays.

# License
PHP JSON Pointer is licensed under MIT license.
