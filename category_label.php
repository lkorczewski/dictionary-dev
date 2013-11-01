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
	
	public function __construct(Dictionary $dictionary, $label = NULL){
		$this->dictionary = $dictionary;
		
		if($label) $this->label = $label;
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	public function set_id($id){
		$this->id = $id;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// setting value
	//------------------------------------------------
	
	public function set($label){
		$this->label = $label;
	}
	
	public function get(){
		return $this->label;
	}
	
}

?>
