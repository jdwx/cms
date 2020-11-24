<?php declare( strict_types = 1 );


namespace JDWX\CMS\Tests;


use InvalidArgumentException;
use JDWX\CMS\InputVariables;
use PHPUnit\Framework\TestCase;


/**
 * @covers \JDWX\CMS\BaseVariables
 * @covers \JDWX\CMS\InputVariables
 */
class InputVariablesTest extends TestCase {


    public function testStrings() : void {
        $inp = new InputVariables([ "foo" => "bar" ], false, false );
        self::assertEquals( "bar", $inp[ "foo" ] );
        $inp[ "foo" ] = "baz";
        self::assertEquals( "baz", $inp[ "foo" ] );
        self::assertArrayHasKey( "foo", $inp );
        unset( $inp[ "foo" ] );
        self::assertArrayNotHasKey( "foo", $inp );
    }


    public function testInvalidExistsKey() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([ "foo" => "bar" ] );
        $x = isset( $inp[ 5 ] );
        $inp[ "baz" ] = $x;
    }


    public function testInvalidGetKey() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([ "foo" => "bar" ] );
        $inp[ 5 ];
    }


    public function testInvalidSetKey() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([ "foo" => "bar" ] );
        $inp[ 5 ] = 2;
        $inp[ "foo" ];
    }


    public function testInvalidSetValueArray() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([]);
        $inp[ "foo" ] = [ "bar", "baz" ];
        $inp[ "foo" ];
    }


    public function testInvalidSetValueInt() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([]);
        $inp[ "foo" ] = 6;
        $inp[ "foo" ];
    }


    public function testInvalidUnsetKey() : void {
        $this->expectException( InvalidArgumentException::class );
        $inp = new InputVariables([ "foo" => "bar" ] );
        unset( $inp[ 5 ] );
    }



}


