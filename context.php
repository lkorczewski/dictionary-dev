<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';

class Context {
	private $dictionary;
	
	private $id;
	private $context;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $context = null){
		$this->dictionary = $dictionary;
		
		if($context) $this->context = $context;
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
	// value management
	//------------------------------------------------
	
	function set($context){
		$this->context = $context;

		return $this;
	}
	
	function get(){
		return $this->context;
	}
	
}

