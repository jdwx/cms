<?php declare( strict_types = 1 );


namespace JDWX\CMS;


interface IPage {


    public function __toString() : string;
    public function link( string $i_stLink ) : string;
    public function run() : void;
    public function setCMS( CMS $i_cms ) : void;


}
