<?php

//========================================================
// Abstraction of translation node
//--------------------------------------------------------
// Contains:
//  - node id
//  - translations
//========================================================

namespace Dictionary;

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/translation.php';

abstract class Node {
	
	protected $dictionary;
	
	private $node_id;
	
	private $translations = array();
	private $translation_iterator = 0;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		
		$this->dictionary = $dictionary;
		
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
