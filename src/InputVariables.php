<?php


declare( strict_types =  1 );


namespace JDWX\CMS;


class InputVariables implements \ArrayAccess {


	private $r;


	public function __construct( array $i_r ) {
		foreach ( $i_r as $stKey => $stValue ) 
			if ( 1 !== preg_match( '/^[a-z0-9_]+$/', $stKey ) )
				throw new \Exception(
					"Input variable has invalid characters: {$stKey}"
				);
		$this->r = $i_r;
	}


	/** @param string|array|null $i_xDefaultValue 
		@return string|array
	 */
	private function _get( string $i_stName,
						   $i_xDefaultValue = null ) {

		if ( array_key_exists( $i_stName, $this->r ) )
			return $this->r[ $i_stName ];

		if ( ! is_null( $i_xDefaultValue ) )
			return $i_xDefaultValue;

		throw new \Exception( "No value for {$i_stName}" );

	}


	public function getArray( string $i_stName,
							  ?array $i_rDefaultValue = null ) : array {
		$x = $this->_get( $i_stName, $i_rDefaultValue );
		if ( is_array( $x ) )
			return $x;
		throw new \Exception( "The value for {$i_stName} is not an array." );
	}


	public function offsetExists( $i_stName ) : bool {
		assert( is_string( $i_stName ) );
		return array_key_exists( $i_stName, $this->r );
	}


	public function offsetGet( $i_stName ) : string {
		assert( is_string( $i_stName ) );
		$x = $this->_get( $i_stName );
		if ( is_string( $x ) )
			return $x;
		throw new \Exception( "The value for {$i_stName} is not a string." );
	}


	public function offsetSet( $i_stName, $i_xValue ) : void {
		assert( is_string( $i_stName ) );
		if ( is_string( $i_xValue ) || is_array( $i_xValue ) ) {
			$this->r[ $i_stName ] = $i_xValue;
			return;
		}
		throw new \Exception(
			"The new value of {$i_stName} must be a string or array."
		);
	}


	public function offsetUnset( $i_stName ) : void {
		assert( is_string( $i_stName ) );
		unset( $this->r[ $i_stName ] );
	}


}


