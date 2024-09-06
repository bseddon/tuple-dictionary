# tuple-dictionary
A dictionary for PHP that allows keys to be include any components of any type

## What is it about?

PHP dictionaries (indexed arrays) only allow the key to be a simple PHP type: string or integer.  However, sometimes it's helpful to be able to index values by a multi-part key which are commonly used in databases for non-primary indexes.

Dictionaries implemented in Python and some other languages allow dictionary keys to be created from tuples containing any valid type.  PHP has a tuple type: the simple array.  So making a dictionary that is keyed by a list of values should be possible - and it is.

Note that this class is not focused on performance.  If you have millions of items in an array then it is probably going to be possible to create customized code that will process those million items more efficiently.

## Examples

The following few lines show how the TupleDictionary can be used.  Key element values can be value types like strings and integers like the built-in dictionary but they can also be objects and arrays as well.  So to begin with a couple of objects will be created.  Note these do not have any properties but they can and minimal objects are used to avoid extraneous complexity.

```php
$obj1 = new \stdClass();
$obj2 = new \stdClass();
```

Here are a couple of keys as examples.  They don't mean any thing.  They are only intended to show that any type can be used to define a key. In $key1 one of the objects is used, an associative array is used pointing to the other object. Two null values are also used.

Note that key elements are not named so the position or order of the element in the key array is important.  Unless the exact order is reproduced the key will be regarded as being different. In examples like this it looks arbitrary but in a real world example it will make sense because the caller will know the 'meaning' of the element in each position.

```php
$key1 = array(
	$obj1,
	array( 'tick' => $obj2 ),
	null,
	null,
	"x",
);

$key2 = array(
	$obj2,
	array( $dict ),
	"z",
);
```

Create an instance of the TupleDictionary

```php
$dict = new \TupleDictionary();
```

Time to add some values and keys.  Note the key is passed as an array and the elements can be of any type.  There can be different numbers of elements used in each key.  The examples show using keys made from one of the objects, an array, a string and a 'null' value.

```php
$dict->addValue( $key1, 1 );
$dict->addValue( $key2, 2 );
```

Items are retrieved by keys and all the used keys can be accessed.  The **getValue** function accepts a key and an optional default value to return if the key does not exist (which will be 'null' if a default is not provided).

```php
$keys = $dict->getKeys();
foreach ( $keys as $key )
{
	$value = $dict->getValue( $key, 'xx' );
}
```

The keys can be reused or re-created.  The in-memory instance of the key is not important only the element values are significant.

```php
$key2 = array(
	$obj2,
	array( $dict ),
	"z",
);

$value = $dict->getValue( $key1, "yy" );
$value = $dict->getValue( $key2 );
```

In this example, $key3 looks very much like $key1 execpt the array index is 'tock' instead of 'tick'.  The purpose of this example is to show that when a array is used as a key element the index values of the array are significant.  Using this key the default value 'yy' will be returned.

```php
$key3 = array(
	$obj1,
	array( 'tock' => $obj2 ),
	null,
	null,
	"x",
);

$value = $dict->getValue( $key3, "yy" );
```

Like a built-in dictionary the items can be deleted by key value.  After deleting the $keys array will contain only one element.

```php
$result = $dict->delete( $key1 );
$keys = $dict->getKeys();
```

## Inside

All keys are turned into hashes and then one grand hash is created.  This grand hash becomes the key in a standard PHP associative array against which both the keys and the values are stored.  When a get or delete request is received again a grand hash is created from the keys and the grand hash is used to determine which values to process.

**Caution** Computing hashes for strings, numbers and null is straight forward.  The internal id of a class instance is by calling the built-in function **spl_object_hash**.  However, a hash for arrays is computed by examining and hashing each element.  If an element is a nested array then each of its elements is examined and hashed recursively.  This means if the array is deeply nested the performance will be knocked.

## Possible future extensions

### Typing the keys

In the current implementation values added can be keyed by any combination of key values.  That is, the keys for different values do not need to include the same number of elements and the elements do not have to be of consistent types.

This provides great flexibility.  If the values are items from an XML document the key elements might be the element tag names used to identify the path to the item.  The number and values of the key elements are going to be different for different items.

But sometimes it will be useful to be able to define the number of elements permitted in a key and, perhaps, the type of values permitted for each element.

### Allowing access to all values for a sub set of the key elements

In the current implementation it is necesssary to request a value by passing a key containing exactly the correct key elements.  It may be useful to be able to pass a value for just the first or the first n key elements and retrieve all values indexed using a subset of the key elements.
