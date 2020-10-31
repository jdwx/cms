<?php


declare( strict_types = 1 );


namespace JDWX\CMS;


use JDWX\HTML5\Document;


abstract class Page {


	protected CMS $cms;

	protected Document $doc;


	public function __construct() {
		$this->doc = new Document();
	}


	public function __toString() : string {
		return ( string ) $this->doc;
	}


	abstract protected function body() : void;


	abstract protected function head() : void;


	public function link( string $i_stLink ) : string {
		return $this->cms->link( $i_stLink );
	}


	public function run() : void {
		$this->head();
		$this->body();
	}


	public function setCMS( CMS $i_cms ) : void {
		$this->cms = $i_cms;
	}


}


