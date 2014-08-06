<?php

require_once __DIR__.'/dictionary.php';

class Source {
	
	private $dictionary;
	
	private $label;
	private $reference;
	
	private $author;
	private $title;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
	}
	
	//------------------------------------------------
	// managing reference to source list
	//------------------------------------------------
	
	function set_reference($label){
		$this->label = $label;
		$this->reference = $this->dictionary->sources[$label];
	}
	
	function is_reference(){
		if(isset($this->reference)) return true;
		
		return false;
	}
	
	function get_label($label){
		
	}

	//------------------------------------------------
	// managing author
	//------------------------------------------------
	
	function set_author($author){
		$this->author = $author;
	}
	
	function get_author(){
		
		if(isset($this->reference)){
			$author = $this->reference['author'];
		} else {
			$author = $this->author;
		}
		
		return $author;
		
	}
	
	//------------------------------------------------
	// managing title
	//------------------------------------------------

	function set_title($title){
		$this->author = $title;
	}
	
	function get_title(){
		
		if(isset($this->reference)){
			$title = $this->reference['title'];
		} else {
			$title = $this->author;
		}
		
		return $title;
		
	}
	
}

