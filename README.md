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

Create an instance of the TupleDictionary

```php
$dict = new \TupleDictionary();
```

Time to add some values and keys.  Note the key is passed as any array and the elements can be of any time.  There can be different numbers of elements used in each key.  The examples show using keys made from one of the objects, an array, a string and a 'null' value.

```php
$dict->addValue( array( $obj1, array( $dict ), null, "x" ), 1 );
$dict->addValue( array( $obj2, array( $dict ), "z" ), 2 );
```

Items are retrieved by keys and all the used key can be added.

```php
$keys = $dict->getKeys();
foreach ( $keys as $key )
{
	$value = $dict->getValue( $key, 'xx' );
}
```

Like a built-in dictionary the items can be deleted by key value.

```php
$result = $dict->delete( array( $obj1, array( $dict ), null, "x" ) );
$keys = $dict->getKeys();
```

## Possible future extensions

### Typing the keys

In the current implementation values added can be keyed by any combination of key values.  That is, the keys for different values do not need to include the same number of elements and the elements do not have to be of consistent types.

This provides great flexibility.  If the values are items from an XML document the key elements might be the element tag names used to identify the path to the item.  The number and values of the key elements are going to be different for different items.

But sometimes it will be useful to be able to define the number of elements permitted in a key and, perhaps, the type of values permitted for each element.

### Allowing access to all values for a sub set of the key elements

In the current implementation it is necesssary to request a value by passing a key containing exactly the correct key elements.  It may be useful to be able to pass a value for just the first or the first n key elements and retrieve all values indexed using a subset of the key elements.
