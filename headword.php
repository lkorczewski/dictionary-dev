<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

class Headword {
	private $dictionary;
	
	private $id;
	private $headword;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $headword = ''){
		$this->dictionary = $dictionary;
		if($headword) $this->headword = $headword;
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
		return $this;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// headword managemenet
	//------------------------------------------------
	
	function set($headword){
		$this->headword = $headword;
		return $this;
	}
	
	function get(){
		return $this->headword;
	}
	
}

?>
