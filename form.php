<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

class Form {
	private $dictionary;
	
	private $id;
	private $label;
	private $form;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $label = NULL, $form = NULL){
		$this->dictionary = $dictionary;
		
		if($label) $this->label = $label;
		if($form) $this->form = $form;
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
	// managing label
	//------------------------------------------------
	
	function set_label($label){
		$this->label = $label;
	}
	
	function get_label(){
		return $this->label;
	}
	
	//------------------------------------------------
	// managing form
	//------------------------------------------------
	
	function set_form($form){
		$this->form = $form;
	}
	
	function get_form(){
		return $this->form;
	}
}

?>
