<?php

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/headword_node.php';
require_once __DIR__.'/phrase.php';
require_once __DIR__.'/translation.php';

class Sense extends Headword_Node {
	
	private $id;
	
	private $label;
	
	private $phrases;
	private $phrases_iterator;
	
	private $senses;
	private $sense_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->phrases = array();
		$this->phrase_iterator = 0;
		
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
	// label management
	//------------------------------------------------
	
	function set_label($label){
		$this->label = $label;
	}
	
	function get_label(){
		return $this->label;
	}
	
	//------------------------------------------------
	// phrase management
	//------------------------------------------------
	
	function add_phrase(){
		$phrase = new Phrase($this->dictionary);
		$this->phrases[] = $phrase;
		
		return $phrase;
	}
	
	function get_phrase(){
		if(!isset($this->phrases[$this->phrase_iterator])) return false;
		
		$phrase = $this->phrases[$this->phrase_iterator];
		$this->phrase_iterator++;
		
		return $phrase;
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
