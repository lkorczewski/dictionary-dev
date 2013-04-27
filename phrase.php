<?php

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/translation.php';

class Phrase {
	
	private $data;
	
	private $id;
	private $node_id;
	
	private $phrase;
	
	private $translation;
	private $translation_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
		$this->data = $dictionary->get_data();
		
		$this->translations = array();
		$this->translation_iterator = 0;
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
	// phrase management
	//------------------------------------------------
	
	function set($phrase){
		$this->phrase = $phrase;
	}
	
	function get(){
		return $this->phrase;
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

}

?>