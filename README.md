PHP JSON Pointer
================
This library allows usage of RFC-6901-compliant JSON pointers with PHP
 variables.

#License
PHP JSON Pointer is licensed under MIT license.

#Documentation
To get ready to access data with JSON Pointer links you need just 3 simple steps:

1. Create an object of `\Remorhaz\JSONPointer\Pointer` class by calling it's static `factory()` method.
2. Link data to the pointer object by calling it's `setData()` method.
3. Set up JSON Pointer link text by calling it's `setText()` method.

After doing so, you can call `test()`, `read()` and `write()` methods to access data.

##Example of usage
```php
<?php

use \Remorhaz\JSONPointer\Pointer;

// Setting up data.
$data = (object) [
    'a' => (object) [
        'b' => ['c', 'd', 'e'],
    ],
];

// Setting up pointer.
$link = '/a/b/1';
$pointer = Pointer::factory()
    ->setData($data)
    ->setText($link);
    
// Readind value.
echo $pointer->read();  // d

// Writing value
echo $pointer
    ->write('f')        // Sets $data->a->b[1] to 'f'.
    ->read();           // f
    
// Testing value.
$result = $pointer->test(); // Sets $result to TRUE.
$link = '/a/c';         // Link to non-existing property
$result = $pointer
    ->setText($link)
    ->test();           // Sets $result to FALSE.
    
// Treating PHP objects as arrays (not compliant with RFC6901, but
// it's the only way to access non-numeric index in PHP array).
$subData = ['g' => 2, 'h' => 3];
$link = '/a/c/g';       // Link to non-numeric index of array.
$result = $pointer
    ->write($subData)   // Sets $data->a->c to ['g' => 2, 'h' => 3].
    ->setText($link)
    ->test();           // Sets $result to FALSE.
$result $pointer
    ->setOptions(Pointer::OPTION_NON_NUMERIC_ARRAY_INDICES)
    ->test();           // Sets $result to TRUE.
echo $pointer->read();  // 2    
```
