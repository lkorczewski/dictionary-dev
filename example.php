<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

class Example {

	private $data;
	
	private $id;
	private $node_id;
	
	private $example;
	private $source;
	
	private $translation;
	private $translation_iterator;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->data = $dictionary->get_data();
		
		$this->translations = [];
		$this->translation_iterator = 0;
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
	// example management
	//------------------------------------------------
	
	function set($example){
		$this->example = $example;
	}
	
	function get(){
		return $this->example;
	}

	//------------------------------------------------
	// source management
	//------------------------------------------------
	
	function make_source(){
		$source = new Source($this->dictionary);
		$this->source = $source;
		
		return $source;
	}
	
	function get_source(){
		return $this->source;
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

