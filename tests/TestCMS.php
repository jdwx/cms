<?php


namespace JDWX\CMS\Tests;


use JDWX\CMS\CMS;
use JDWX\CMS\IPage;


/**
 * Class TestCMS
 *
 * This is a concrete child of the CMS class used for testing it.  Contrast the CMSTest class, which
 * contains the actual tests.
 *
 * @package JDWX\CMS\Tests
 *
 */
class TestCMS extends CMS {


    public function __construct( string $i_stPrefix ) {
        parent::__construct(
            [],         #  GET
            [],         #  POST
            [],         #  COOKIE
            [],         #  FILES
            $i_stPrefix
        );
    }


    public function checkAddRoute( string $i_stURI, string $i_stFile ) : void {
        $this->addRoute( $i_stURI, $i_stFile );
    }


    public function checkGetPage( string $i_stPage ) : IPage {
        return $this->getPage( $i_stPage );
    }


    public function checkMapRouteArray( array $i_rURI ) : ?string {
        return $this->mapRouteArray( $i_rURI );
    }


    public function checkParseRoute( string $i_stURI ) : array {
        return $this->parseRoute( $i_stURI );
    }


    public function setup() : void {
    }


}