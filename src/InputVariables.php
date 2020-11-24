<?php declare( strict_types = 1 );


namespace JDWX\CMS;


use ArrayAccess;
use InvalidArgumentException;


/**
 * Class InputVariables
 *
 * @package JDWX\CMS
 *
 *          This is designed to provide array-style access to input variables for convenience.
 *          However, it intentionally supports only strings.  If you have a situation that
 *          requires array values, use the getArray()/setArray() methods from the base class
 *          to access them with type-safety.
 */


class InputVariables extends BaseVariables implements ArrayAccess {


    public function offsetExists( $i_stName ) : bool {
        if ( ! is_string( $i_stName ) ) {
            throw new InvalidArgumentException( "The key {$i_stName} is not a string." );
        }
        return $this->exists( $i_stName );
    }


    public function offsetGet( $i_stName ) : string {
        if ( ! is_string( $i_stName ) ) {
            throw new InvalidArgumentException( "The key {$i_stName} is not a string." );
        }
        return $this->getString( $i_stName );
    }


    public function offsetSet( $i_stName, $i_stValue ) : void {
        if ( ! is_string( $i_stName ) ) {
            throw new InvalidArgumentException( "The key {$i_stName} is not a string." );
        }
        if ( is_string( $i_stValue ) ) {
            $this->setString( $i_stName, $i_stValue );
            return;
        }
        throw new InvalidArgumentException( "The new value of {$i_stName} must be a string." );
    }


    public function offsetUnset( $i_stName ) : void {
        if ( ! is_string( $i_stName ) ) {
            throw new InvalidArgumentException( "The key {$i_stName} is not a string." );
        }
        $this->unset( $i_stName );
    }


}


