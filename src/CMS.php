<?php declare( strict_types = 1 );


namespace JDWX\CMS;


use DomainException;
use UnexpectedValueException;


require_once __DIR__ . '/InputVariables.php';
require_once __DIR__ . '/Page.php';


abstract class CMS {


    public InputVariables $COOKIE;

    public InputVariables $GET;

    public InputVariables $POST;

    public array $rFiles;

    private array $rRouteMap = [];

    private string $stPrefix;

    private int $uPrefix;


    public function __construct( ?array $i_rGet = null,
                                 ?array $i_rPost = null,
                                 ?array $i_rCookie = null,
                                 ?array $i_rFiles = null,
                                 ?string $i_nstPrefix = null ) {

        $this->COOKIE = new InputVariables( $i_rCookie ?? $_COOKIE );
        $this->GET = new InputVariables( $i_rGet ?? $_GET );
        $this->POST = new InputVariables( $i_rPost ?? $_POST );
        $this->rFiles = $i_rFiles ?? $_FILES;

        $stPrefix = $i_nstPrefix ?? $_SERVER[ 'PHP_SELF' ];
        if ( "" === $stPrefix ) {
            $stPrefix = "/";
        }

        if ( "/" !== $stPrefix ) {
            if ( 0 !== strpos( $stPrefix, '/' ) ) {
                throw new DomainException( "Prefix {$stPrefix} must begin with /" );
            }
            $i = strrpos( $stPrefix, "/" );
            assert( is_int( $i ) ); ##  We ensured this cannot fail above.
            $stPrefix = substr( $stPrefix, 0, $i + 1 );
        }
        $this->stPrefix = $stPrefix;
        $this->uPrefix = strlen( $this->stPrefix );

    }


    protected function addRoute( string $i_stURI, string $i_stFile ) : void {
        if ( '/' === $i_stURI[ 0 ] ) {
            $i_stURI = substr( $i_stURI, 1 );
        }
        $rURI = explode( '/', $i_stURI );
        $where =& $this->rRouteMap;
        foreach ( $rURI as $arg ) {
            if ( ! isset( $where[ $arg ] ) ) {
                $where[ $arg ] = [];
            }
            $where =& $where[ $arg ];
        }
        $where[ "{END}" ] = $i_stFile;
    }


    public function error404() : void {
        header( "Status: 404" );
        echo "<p>Not found.</p>";
    }


    protected function getPage( string $i_stPage ) : IPage {
        $obj = require( $i_stPage );
        $obj->setCMS( $this );
        $obj->run();
        return $obj;
    }


    public function getPrefix() : string {
        return $this->stPrefix;
    }


    public function link( string $i_stLink ) : string {
        if ( '/' === $i_stLink ) {
            return $this->stPrefix;
        }
        if ( '/' === $this->stPrefix ) {
            return $i_stLink;
        }
        if ( '/' === $i_stLink[ 0 ] ) {
            $i_stLink = $this->stPrefix . substr( $i_stLink, 1 );
        }
        return $i_stLink;
    }


    public function mapRouteArray( array $i_rURI ) : ?string {

        // echo "<p>map = '", print_r( $this->rRouteMap, true ), "'</p>";
        $where = $this->rRouteMap;
        foreach ( $i_rURI as $st ) {
            if ( ! array_key_exists( $st, $where ) ) {
                $this->error404();
                return null;
            }
            $where = $where[ $st ];
        }

        // echo "<p>page = ", $where, "</p>";

        return $where;

    }


    protected function parseRoute( string $i_stURI ) : array {

        ##  Sanity-check the URI
        if ( 1 !== preg_match( '/^[a-z0-9\/_]+$/', $i_stURI ) ) {
            throw new UnexpectedValueException( "Invalid characters in URI: {$i_stURI}" );
        }

        ##  Blow up if the requested URI is outside our prefix.
        if ( 0 !== strpos( $i_stURI, $this->stPrefix ) ) {
            throw new UnexpectedValueException( "Requested route outside prefix: {$i_stURI}" );
        }

        ##  Remove the prefix.
        $i_stURI = substr( $i_stURI, $this->uPrefix );

        $rURI = explode( '/', $i_stURI );
        $rURI[] = "{END}";

        // echo "<p>uri string = '", $i_stURI, "'</p>";
        // echo "<p>uri array = '", print_r( $rURI, true ), "'</p>";

        return $rURI;

    }


    public function route( string $i_stURI ) : void {

        $rURI = $this->parseRoute( $i_stURI );

        $where = $this->mapRouteArray( $rURI );
        if ( ! is_string( $where ) ) {
            return;
        }

        $obj = $this->getPage( $where );
        echo $obj;

    }


    abstract public function setup() : void;


}


