<?php


declare( strict_types = 1 );


namespace JDWX\CMS;


use UnexpectedValueException;


require_once __DIR__ . '/InputVariables.php';
require_once __DIR__ . '/Page.php';


abstract class CMS {


	public InputVariables $COOKIE;

	public InputVariables $GET;

	public InputVariables $POST;

	private array $rRouteMap = [];

	private string $stPrefix;

	private int $uPrefix;


	public function __construct( ?array $i_rGet = null,
								 ?array $i_rPost = null,
								 ?array $i_rCookie = null,
								 ?array $i_rFiles = null,
								 ?string $i_stPrefix = null ) {
		$this->COOKIE   = new InputVariables( $i_rCookie ?? $_COOKIE );
		$this->GET      = new InputVariables( $i_rGet ?? $_GET );
		$this->POST     = new InputVariables( $i_rPost ?? $_POST );
		$this->stPrefix = dirname( $i_stPrefix ?? $_SERVER[ 'PHP_SELF' ] );
		if ( '/' === $this->stPrefix ) {
            $this->stPrefix = "";
        }
		$this->uPrefix  = strlen( $this->stPrefix );
	}


	protected function addRoute( string $i_stURI, string $i_stFile ) : void {
		if ( '/' === $i_stURI[0] ) {
            $i_stURI = substr($i_stURI, 1);
        }
		$rURI = explode( '/', $i_stURI );
		$where =& $this->rRouteMap;
		foreach ( $rURI as $arg ) {
			if ( ! isset( $where[ $arg ] ) ) {
                $where[$arg] = [];
            }
			$where =& $where[ $arg ];
		}
		$where[ "{END}" ] = $i_stFile;
	}


	public function error404() : void {
		header( "Status: 404" );
		echo "<p>Not found.</p>";
	}


	public function link( string $i_stLink ) : string {
		if ( '/' === $i_stLink ) {
            return $this->stPrefix;
        }
		if ( '/' === $i_stLink[ 0 ] ) {
			$i_stLink = $this->stPrefix . $i_stLink;
		}
		return $i_stLink;
	}


	public function route( string $i_stURI ) : void {
		if ( 1 !== preg_match( '/^[a-z0-9\/_]+$/', $i_stURI ) ) {
            throw new UnexpectedValueException( "Invalid characters in URI: {$i_stURI}" );
        }
		if ( $this->uPrefix > 0 && 0 !== strpos( $i_stURI, $this->stPrefix ) ) {
            throw new UnexpectedValueException( "Requested route outside prefix: {$i_stURI}" );
        }
        $i_stURI = substr( $i_stURI, $this->uPrefix );
		if ( '/' === $i_stURI[ 0 ] ) {
            $i_stURI = substr($i_stURI, 1);
        }
		$rURI = explode( '/', $i_stURI );
		$rURI[] = "{END}";
		// echo "<p>uri string = '", $i_stURI, "'</p>";
		// echo "<p>uri array = '", print_r( $rURI, true ), "'</p>";
		// echo "<p>map = '", print_r( $this->rRouteMap, true ), "'</p>";
		$where = $this->rRouteMap;
		foreach ( $rURI as $st ) {
			if ( ! array_key_exists( $st, $where ) ) {
				$this->error404();
				return;
			}
			$where = $where[ $st ];
		}
		$obj = require( $where );
		$obj->setCMS( $this );
		$obj->run();
		echo $obj;
		// echo "<p>page = ", $where, "</p>";
	}


	abstract protected function setup() : void;


}


