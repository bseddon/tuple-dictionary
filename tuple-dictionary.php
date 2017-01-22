<?php

/**
 * Implements the class TupleDictionary.
 * The class acts as an dictionary (indexed or associative array) but allows keys to be made
 * from any PHP types including objects.
 *
 * @author Bill Seddon
 * @copyright Lyquidity Solutions Limited 2016
 * @license Apache 2
 *          You are welcome to do anything with this code but is provided 'as-is'
 *          and no warranty is expressed or implied
 */

/**
 * Class implementation
 */
class TupleDictionary
{
	/**
	 * The data store
	 * @var array
	 */
	private $data = array();

	/**
	 * The name of the hashing algorithm to use
	 * @var string
	 */
	private $hash_algorithm = 'sha256';

	/**
	 * Constructor
	 *
	 * @param string $hash_algorithm The name of the hash algorithm to use and will be 'sha256' by default.
	 *                               Must be one of the name returned by the PHP function hash_algos().
	 * @throws Exception An exception will be thrown if the name of the algorithm is not recognized.
	 */
	function __construct( $hash_algorithm = 'sha256' )
	{
		if ( ! in_array( strtolower( $hash_algorithm ), hash_algos() ) )
			throw new Exception();

		$this->hash_algorithm = $hash_algorithm;
	}

	/**
	 * Generates hashes for all the elements of the key
	 * @param $elements An array of key elements for which to create the hashes
	 * @return An array of the elements indexed by their hashes and the overall hash
	 */
	private function hashArray( $elements )
	{
		$hashes = array();

		foreach ( $elements as $key => $element )
		{
			if ( is_object( $element ) )
			{
				$hash = spl_object_hash( $element ) . $key;
				$hashes[ $hash ] = $element;
			}
			else if ( is_array( $element ) )
			{
				extract( $this->hashArray( $element ) ) . $key;
				$hashes[ $hash ] = $element;
			}
			else
			{
				$hash = hash( $this->hash_algorithm, $element . $key );
				$hashes[ $hash ] = $element;
			}
		}

		return array( 'hash' => hash( $this->hash_algorithm, serialize( array_keys( $hashes ) ) ), 'element_hashes' => $hashes );
	}

	/**
	 * Add a new node (or replace an existing one) indexed by $key
	 *
	 * @param array $key An array of items to use as an index
	 * @param unknown $value The value to record
	 * @throws \Exception Thrown if no key is provided
	 */
	public function addValue( $key, $value )
	{
		if ( ! isset( $key ) )
			throw new \Exception( "A valid key has not been provided" );

			if ( ! is_array( $key ) )
				$key = array( $key );

				$result = $this->hashArray( $key );

				// $this->data['hashes'][ $result['hash'] ] = $result['element_hashes'];
				$this->data['values'][ $result['hash'] ] = $value;
				$this->data['keys'][ $result['hash'] ] = $key;
	}

	/**
	 * Get a value for for a key
	 *
	 * @param array $key An array of items to use as an index
	 * @param unknown $default
	 * @return string|mixed The value corresponding to the $key or $default if the key is not found
	 */
	public function &getValue( $key, $default = null )
	{
		$result = $this->hashArray( $key );

		return $this->getValueByHash( $result['hash'], $default );
	}

	/**
	 * Get a value for a specific hash
	 *
	 * @param string $hash
	 * @param mixed $default
	 * @return $array The value corresponding to the hash
	 */
	public function &getValueByHash( $hash, $default = null )
	{
		if ( ! isset( $this->data['values'][ $hash ] ) )
		{
			return $default;
		}

		return $this->data['values'][ $hash ];
	}

	/**
	 * Get an array of all the keys used
	 */
	public function getKeys()
	{
		return array_values( $this->data['keys'] );
	}

	/**
	 *
	 * @param array $key An array of items to use as an index
	 * @return boolean True if the key exists and the item is deleted or false
	 */
	public function delete( $key )
	{
		$result = $this->hashArray( $key );

		if ( ! isset( $this->data['values'][ $result['hash'] ] ) )
			return false;

		unset( $this->data['values'][ $result['hash'] ] );

		if ( ! isset( $this->data['keys'][ $result['hash'] ] ) )
			return false;

		unset( $this->data['keys'][ $result['hash'] ] );
		return true;
	}

}
