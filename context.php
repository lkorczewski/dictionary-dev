<?php

require_once __DIR__ . '/dictionary.php';

class Context {
	private $dictionary;
	
	private $id;
	private $context;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $context = NULL){
		$this->dictionary = $dictionary;
		
		if($context) $this->label = $context;
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// value management
	//------------------------------------------------
	
	function set($context){
		$this->context = $context;
	}
	
	function get(){
		return $this->context;
	}
	
}

?>
