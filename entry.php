<?php

//require_once __DIR__.'/data.php';
require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/sense.php';

class Entry {
	private $dictionary;
	
	private $id;
	private $node_id;
	
	private $headword;
	
	private $forms;
	private $form_iterator;
	
	private $senses;
	private $sense_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
		$this->database = $dictionary->get_database();
		
		$this->forms = array();
		$this->form_iterator = 0;
		
		$this->senses = array();
		$this->sense_iterator = 0;
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
	// node id management
	//------------------------------------------------
	
	function set_node_id($node_id){
		$this->node_id = $node_id;
	}
	
	function get_node_id(){
		return $this->node_id;
	}
	
	//------------------------------------------------
	// headword management
	//------------------------------------------------
	
	function set_headword($headword){
		$this->headword = $headword;
	}
	
	function get_headword(){
		return $this->headword;
	}

	//------------------------------------------------
	// form management
	//------------------------------------------------
	
	function add_form(){
		$form = new Form($this->dictionary);
		$this->forms[] = $form;
		
		return $form;
	}
	
	function get_form(){
		if(!isset($this->forms[$this->form_iterator])) return false;
		
		$form = $this->forms[$this->form_iterator];
		$this->form_iterator++;
		
		return $form;
	}
	
	//------------------------------------------------
	// sense management
	//------------------------------------------------
	
	function add_sense(){
		$sense = new Sense($this->dictionary);
		$this->senses[] = $sense;
		
		return $sense;
	}
	
	function get_sense(){
		if(!isset($this->senses[$this->sense_iterator])) return false;
		
		$sense = $this->senses[$this->sense_iterator];
		$this->sense_iterator++;
		
		return $sense;
	}
	
}

?>
