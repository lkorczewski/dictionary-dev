<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';

class Category_Label {
	private $dictionary;
	
	private $id;
	private $label;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $label = NULL){
		$this->dictionary = $dictionary;
		
		if($label) $this->label = $label;
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
	// setting value
	//------------------------------------------------
	
	function set($label){
		$this->label = $label;
	}
	
	function get(){
		return $this->label;
	}
	
}

