<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__.'/node_interface.php';

interface Node_With_Headwords extends Node_Interface {
	function add_headword();
	function get_headword();
}

//----------------------------------------------------------------------------

require_once __DIR__.'/../headword.php';

trait Has_Headwords {
	
	private $headwords = [];
	private $headword_iterator = 0;
	
	//------------------------------------------------------------------------
	// headword management
	//------------------------------------------------------------------------
	
	function add_headword(){
		$headword = new Headword($this->dictionary);
		$this->headwords[] = $headword;
		
		return $headword;
	}
	
	function get_headword(){
		if(!isset($this->headwords[$this->headword_iterator])){
			return false;
		}
		
		$headword = $this->headwords[$this->headword_iterator];
		$this->headword_iterator++;
		
		return $headword;
	}
	
}

