<?php

//require_once __DIR__.'/data.php';
require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/headword_node.php';
require_once __DIR__.'/sense.php';

class Entry extends Headword_Node {
	
	private $id;
	
	private $headword;
	
	private $senses;
	private $sense_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		$this->database = $dictionary->get_database();
		
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
	// headword management
	//------------------------------------------------
	
	function set_headword($headword){
		$this->headword = $headword;
	}
	
	function get_headword(){
		return $this->headword;
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
