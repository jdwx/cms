<?php


declare( strict_types = 1 );


namespace JDWX\CMS;


require_once __DIR__ . '/InputVariables.php';
require_once __DIR__ . '/Page.php';


abstract class CMS {


	/** @var InputVariables */
	public $COOKIE;

	/** @var InputVariables */
	public $GET;

	/** @var InputVariables */
	public $POST;

	/** @var array */
	private $rRouteMap = [];

	/** @var string */
	private $stPrefix; 

	/** @var int */
	private $uPrefix;


	public function __construct( ?array $i_rGet = null,
								 ?array $i_rPost = null,
								 ?array $i_rCookie = null,
								 ?array $i_rFiles = null,
								 ?string $i_stPrefix = null ) {
		$this->COOKIE   = new InputVariables( $i_rCookie ?? $_COOKIE );
		$this->GET      = new InputVariables( $i_rGet ?? $_GET );
		$this->POST     = new InputVariables( $i_rPost ?? $_POST );
		$this->stPrefix = dirname( $i_stPrefix ?? $_SERVER[ 'PHP_SELF' ] );
		$this->uPrefix  = strlen( $this->stPrefix );
	}


	protected function addRoute( string $i_stURI, string $i_stFile ) : void {
		if ( '/' == substr( $i_stURI, 0, 1 ) )
			$i_stURI = substr( $i_stURI, 1 );
		$rURI = explode( '/', $i_stURI );
		$where =& $this->rRouteMap;
		foreach ( $rURI as $arg ) {
			if ( ! isset( $where[ $arg ] ) )
				$where[ $arg ] = [];
			$where =& $where[ $arg ];
		}
		$where[ "{END}" ] = $i_stFile;
	}


	public function error404() {
		header( "Status: 404" );
		echo "<p>Not found.</p>";
	}


	public function link( string $i_stLink ) : string {
		if ( '/' == substr( $i_stLink, 0, 1 ) ) {
			$i_stLink = $this->stPrefix . $i_stLink;
		}
		return $i_stLink;
	}


	public function route( string $i_stURI ) : void {
		if ( 1 !== preg_match( '/^[a-z0-9\/_]+$/', $i_stURI ) )
			throw new \Exception( "Invalid characters in URI: {$i_stURI}" );
		if ( substr( $i_stURI, 0, $this->uPrefix ) == $this->stPrefix )
			$i_stURI = substr( $i_stURI, $this->uPrefix );
		if ( '/' == substr( $i_stURI, 0, 1) )
			$i_stURI = substr( $i_stURI, 1 );
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
		$obj = require_once( $where );
		$obj->setCMS( $this );
		$obj->run();
		echo $obj;
		// echo "<p>page = ", $where, "</p>";
	}


	abstract function setup() : void;


}


