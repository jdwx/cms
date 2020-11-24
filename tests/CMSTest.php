<?php


namespace JDWX\CMS\Tests;


require_once __DIR__ . "/TestCMS.php";


use DomainException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;


/**
 * Class CMSTest
 *
 * This tests the CMS class.  Contrast TestCMS, which is the concrete child of the CMS class needed to test against.
 *
 * @package \JDWX\CMS\Tests
 * @covers \JDWX\CMS\BaseVariables
 * @covers \JDWX\CMS\CMS
 * @covers \JDWX\CMS\Page
 */
class CMSTest extends TestCase {


    public function testCMS() : void {
        $cms = new TestCMS( "/" );
        $cms->setup();
        self::assertEquals( "/", $cms->getPrefix() );
        self::assertEquals("foo/bar", $cms->link( "foo/bar" ) );
        self::assertEquals("/", $cms->link( "/" ) );

        $stFile = __DIR__ . "/route.php";
        $cms->checkAddRoute( "/baz", $stFile );
        $rCheck = $cms->checkParseRoute( "/baz" );
        self::assertEquals( [ "baz", "{END}" ], $rCheck );

        $where = $cms->checkMapRouteArray( $rCheck );
        self::assertEquals( $stFile, $where );

        $page = $cms->checkGetPage( $where );
        $st = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body></body></html>';
        self::assertEquals( $st, ( string ) $page );

        self::assertEquals( "foo", $page->link( "foo" ) );

    }


    /**
     * @runInSeparateProcess
     */
    public function testError404() : void {
        $cms = new TestCMS( "/" );
        ob_start();
        $cms->error404();
        $stCheck = ob_get_clean();
        $stBase = '<p>Not found.</p>';
        self::assertEquals( $stBase, $stCheck );
    }


    public function testParseRouteOutsidePrefix() : void {
        $this->expectException( UnexpectedValueException::class );
        $cms = new TestCMS( "/baz/" );
        $cms->checkAddRoute( "/foo/bar", __DIR__ . "/route.php" );
        $cms->checkParseRoute( "/qux/foo/bar" );
    }


    public function testParseRouteWithInvalidCharacters() : void {
        $this->expectException( UnexpectedValueException::class );
        $cms = new TestCMS( "/baz/" );
        $cms->checkAddRoute( "/foo/bar", __DIR__ . "/route.php" );
        $cms->checkParseRoute( "@&;!" );
    }


    public function testRoute() : void {
        $cms = new TestCMS( "/baz/" );
        $cms->checkAddRoute( "/foo/bar", __DIR__ . "/route.php" );
        ob_start();
        $cms->route( "/baz/foo/bar" );
        $stCheck = ob_get_clean();
        $stBase = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body></body></html>';
        self::assertEquals( $stBase, $stCheck );
    }


    /**
     * @runInSeparateProcess
     */
    public function testRoute404() : void {
        $cms = new TestCMS( "/baz/" );
        $cms->checkAddRoute( "/foo/bar", __DIR__ . "/route.php" );
        ob_start();
        $cms->route( "/baz/foo/qux" );
        $stCheck = ob_get_clean();
        $stBase = '<p>Not found.</p>';
        self::assertEquals( $stBase, $stCheck );
    }


    public function testWithBadPrefix() : void {
        $this->expectException( DomainException::class );
        $cms = new TestCMS( "foo" );
        self::assertEquals( $cms, $cms->getPrefix() );
    }


    public function testWithFilePrefix() : void {
        $cms = new TestCMS( "/foo" );
        self::assertEquals( "/", $cms->getPrefix() );
    }


    public function testWithPrefix() : void {
        $cms = new TestCMS( "/foo/" );
        self::assertEquals( "/foo/", $cms->getPrefix() );
        self::assertEquals("/foo/bar/baz", $cms->link( "/bar/baz" ) );
        self::assertEquals("bar/baz", $cms->link( "bar/baz" ) );
        self::assertEquals("/foo/", $cms->link( "/" ) );
    }


    public function testWithNestedFilePrefix() : void {
        $cms = new TestCMS( "/foo/bar" );
        self::assertEquals( "/foo/", $cms->getPrefix() );
    }


    public function testWithNestedPrefix() : void {
        $cms = new TestCMS( "/foo/bar/" );
        self::assertEquals( "/foo/bar/", $cms->getPrefix() );
    }


    public function testWithNoPrefix() : void {
        $cms = new TestCMS( "" );
        self::assertEquals( "/", $cms->getPrefix() );
    }


}

