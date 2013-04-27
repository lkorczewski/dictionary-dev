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
	
	public function set_reference($label){
		$this->label = $label;
		$this->reference = $this->dictionary->sources[$label];
	}
	
	public function is_reference(){
		if(isset($this->reference)) return true;
		
		return false;
	}
	
	public function get_label($label){
		
	}

	//------------------------------------------------
	// managing author
	//------------------------------------------------
	
	public function set_author($author){
		$this->author = $author;
	}
	
	public function get_author(){
		
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

	public function set_title($title){
		$this->author = $title;
	}
	
	public function get_title(){
		
		if(isset($this->reference)){
			$title = $this->reference['title'];
		} else {
			$title = $this->author;
		}
		
		return $title;
		
	}
	
}

?>