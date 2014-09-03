<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

abstract class Value {
	private $dictionary;
	
	private $id;
	private $value;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $value = null){
		$this->dictionary = $dictionary;
		if($value) $this->value = $value;
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
	
	function set($value){
		$this->value = $value;
		return $this;
	}
	
	function get(){
		return $this->value;
	}
	
}

