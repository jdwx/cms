<?php declare( strict_types = 1 );


namespace JDWX\CMS\Tests;


use InvalidArgumentException;
use JDWX\CMS\BaseVariables;
use LogicException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;


/**
 * Class BaseVariablesTest
 *
 * @package \JDWX\CMS\Tests
 * @covers  \JDWX\CMS\BaseVariables
 */
class BaseVariablesTest extends TestCase {


    public function testArrays() : void {
        $var = new BaseVariables(
            [ "foo" => [ "bar", "baz" ] ],
            true
        );
        self::assertContains( "bar", $var->getArray("foo" ) );
    }


    public function testStrings() : void {

        $var = new BaseVariables( [
                                      "foo" => "bar",
                                      "baz" => "qux",
                                      "XDEBUG_SESSION" => "abc123",
                                  ] );

        self::assertTrue( $var->exists( "foo" ) );
        self::assertFalse( $var->exists( "quux" ) );
        self::assertEquals( "bar", $var->getString( "foo" ) );
        self::assertEquals( "quux", $var->getString( "baaz", "quux" ) );

    }


    public function testGetArrayOnString() : void {
        $this->expectException( InvalidArgumentException::class );
        $var = new BaseVariables( [ "foo" => "bar" ], true );
        $var->getArray( "foo" );
    }


    public function testGetStringOnArray() : void {
        $this->expectException( InvalidArgumentException::class );
        $var = new BaseVariables( [ "foo" => [ "bar", "baz" ] ], true );
        $var->getString( "foo" );
    }


    public function testInvalidGetArray() : void {
        $this->expectException( LogicException::class );
        $var = new BaseVariables( [ "foo" => "bar" ] );
        $var->getArray( "foo" );
    }


    public function testInvalidKeyType() : void {
        $this->expectException( InvalidArgumentException::class );
        $var = new BaseVariables( [
                                      6 => "baz",
                                  ] );
        $var->getString( "foo" );

    }


    public function testInvalidKeyValue() : void {
        $this->expectException( UnexpectedValueException::class );
        $var = new BaseVariables( [
                                      "foo;bar" => "baz",
                                  ] );
        $var->getString( "foo" );
    }


    public function testInvalidValueArray() : void {
        $this->expectException( InvalidArgumentException::class );
        $var = new BaseVariables( [
                                      "foo" => [ "bar", "baz" ],
                                  ] );
        $var->getString( "foo" );
    }

    public function testInvalidValueType() : void {
        $this->expectException( InvalidArgumentException::class );
        $var = new BaseVariables( [
                                      "foo" => 2,
                                  ] );
        $var->getString( "foo" );
    }


    public function testNoKeyWithNoDefault() : void {
        $this->expectException( OutOfBoundsException::class );
        $var = new BaseVariables( [] );
        $var->getString( "foo" );
    }


    public function testSetImmutable() : void {
        $this->expectException( LogicException::class );
        $var = new BaseVariables( [] );
        $var->setString( "foo", "bar" );
    }


    public function testUnset() : void {
        $var = new BaseVariables( [ "foo" => "bar" ], false, false );
        $var->unset( "foo" );
        self::assertEquals( "baz", $var->getString( "foo", "baz" ) );
    }


    public function testUnsetImmutable() : void {
        $this->expectException( LogicException::class );
        $var = new BaseVariables( [ "foo" => "bar" ] );
        $var->unset( "foo" );
    }


}