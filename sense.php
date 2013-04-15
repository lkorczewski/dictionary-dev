<?php

//require_once __DIR__.'/data.php';
require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/translation.php';

class Sense {
	private $dictionary;
	private $data;
	private $databaase;
	
	private $label;
	
	private $translations;
	private $translation_iterator;
	
	private $id;
	private $senses;
	private $sense_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
		$this->data = $dictionary->get_data();
		$this->database = $dictionary->get_database();
		
		$this->translations = array();
		$this->translation_iterator = 0;
		
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
