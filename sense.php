<?php

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/phrase.php';
require_once __DIR__.'/translation.php';

class Sense {
	
	private $dictionary;
	private $data;
	
	private $id;
	private $node_id;
	
	private $label;
	
	private $translations;
	private $translation_iterator;
	
	private $phrases;
	private $phrases_iterator;
	
	private $senses;
	private $sense_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
		$this->data = $dictionary->get_data();
		
		$this->translations = array();
		$this->translation_iterator = 0;
		
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
	// node id management
	//------------------------------------------------
	
	function set_node_id($node_id){
		$this->node_id = $node_id;
	}
	
	function get_node_id(){
		return $this->node_id;
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
	// translation management
	//------------------------------------------------
	
	function add_translation(){
		$translation = new Translation($this->dictionary);
		$this->translations[] = $translation;
		
		return $translation;
	}
	
	function get_translation(){
		if(!isset($this->translations[$this->translation_iterator])) return false;
		
		$translation = $this->translations[$this->translation_iterator];
		$this->translation_iterator++;
			
		return $translation;
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
