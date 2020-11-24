<?php declare( strict_types = 1 );


namespace JDWX\CMS;


use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;
use UnexpectedValueException;


class BaseVariables {


    private array $r = [];
    private bool  $bAllowArrays;
    private bool  $bImmutable = false;


    public function __construct( array $i_r, bool $i_bAllowArrays = false, bool $i_bImmutable = true ) {
        $this->bAllowArrays = $i_bAllowArrays;
        foreach ( $i_r as $stKey => $xValue ) {
            if ( ! is_string( $stKey ) ) {
                throw new InvalidArgumentException( "Input key has invalid tpye: " . gettype( $stKey ) );
            }
            if ( 'XDEBUG_SESSION' === $stKey ) {
                continue;
            }
            if ( is_string( $xValue ) ) {
                $this->setString( $stKey, $xValue );
                continue;
            }
            if ( is_array( $xValue ) ) {
                $this->setArray( $stKey, $xValue );
                continue;
            }
            throw new InvalidArgumentException(
                "Input variable has invalid type: " . gettype( $xValue )
            );
        }
        $this->bImmutable = $i_bImmutable;
    }


    private static function checkKey( string $i_stKey ) : void {
        if ( 1 !== preg_match( '/^[a-z0-9_]+$/', $i_stKey ) ) {
            throw new UnexpectedValueException(
                "Key has invalid characters: {$i_stKey}"
            );
        }
    }


    public function exists( string $i_stKey ) : bool {
        self::checkKey( $i_stKey );
        return array_key_exists( $i_stKey, $this->r );
    }


    /**
     * @param string            $i_stKey
     * @param string|array|null $i_xDefaultValue
     * @return string|array
     * @throws OutOfBoundsException
     */
    private function get( string $i_stKey, $i_xDefaultValue = null ) {

        self::checkKey( $i_stKey );

        if ( array_key_exists( $i_stKey, $this->r ) ) {
            return $this->r[ $i_stKey ];
        }

        if ( ! is_null( $i_xDefaultValue ) ) {
            return $i_xDefaultValue;
        }

        throw new OutOfBoundsException( "No value for {$i_stKey}" );

    }


    public function getArray( string $i_stKey, ?array $i_nrDefaultValue = null ) : array {
        if ( ! $this->bAllowArrays ) {
            throw new LogicException( "Requested an array value for {$i_stKey} when arrays are not allowed." );
        }
        $x = $this->get( $i_stKey, $i_nrDefaultValue );
        if ( is_array( $x ) ) {
            return $x;
        }
        throw new InvalidArgumentException( "The value for {$i_stKey} is not an array." );
    }


    public function getString( string $i_stKey, ?string $i_nstDefaultValue = null ) : string {
        $x = $this->get( $i_stKey, $i_nstDefaultValue );
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new InvalidArgumentException( "The value for {$i_stKey} is not a string." );
    }


    private function set( string $i_stKey, $i_xValue ) : void {
        self::checkKey( $i_stKey );
        if ( $this->bImmutable ) {
            throw new LogicException( "Attempted to set immutable input." );
        }
        $this->r[ $i_stKey ] = $i_xValue;
    }


    public function setArray( string $i_stKey, array $i_rValue ) : void {
        if ( ! $this->bAllowArrays ) {
            throw new InvalidArgumentException( "Set an array value for {$i_stKey} when arrays are not allowed." );
        }
        $this->set( $i_stKey, $i_rValue );
    }


    public function setString( string $i_stKey, string $i_stValue ) : void {
        $this->set( $i_stKey, $i_stValue );
    }


    public function unset( string $i_stKey ) : void {
        self::checkKey( $i_stKey );
        if ( $this->bImmutable ) {
            throw new LogicException( "Attempted to unset immutable input." );
        }
        unset( $this->r[ $i_stKey ] );
    }


}