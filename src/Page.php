<?php


declare( strict_types = 1 );


namespace JDWX\CMS;


abstract class Page {


	/** @var CMS */
	protected $cms;

	/** @var \JDWX\HTML5\Document */
	protected $doc;


	public function __construct() {
		$this->doc = new \JDWX\HTML5\Document(); 
	}


	public function __toString() : string {
		return strval( $this->doc );
	}


	abstract function body() : void;


	abstract function head() : void;


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


