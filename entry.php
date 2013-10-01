<?php

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/headword.php';
require_once __DIR__.'/headword_node.php';
require_once __DIR__.'/sense.php';

class Entry extends Headword_Node {
	
	private $id;
	
	private $headword;
	
	private $headwords;
	private $headword_iterator;
		
	private $phrases;
	private $phrase_iterator;
	
	private $senses;
	private $sense_iterator;
	
	private $comment;
	
	//------------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
		$this->headwords = array();
		$this->headword_iterator = 0;
		
		$this->phrases = array();
		$this->phrase_iterator = 0;
		
		$this->senses = array();
		$this->sense_iterator = 0;
		
	}
	
	//------------------------------------------------------------------------
	// id management
	//------------------------------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------------------------------
	// headword management
	//------------------------------------------------------------------------
	/*
	function set_headword($headword){
		$this->headword = $headword;
	}
	
	function get_headword(){
		return $this->headword;
	}
	*/
	
	function add_headword(){
		$headword = new Headword($this->dictionary);
		$this->headwords[] = $headword;
		
		return $headword;
	}
	
	function get_headword(){
		if(!isset($this->headwords[$this->headword_iterator])) return false;
		
		$headword = $this->headwords[$this->headword_iterator];
		$this->headword_iterator++;
		
		return $headword;
	}
	
	//------------------------------------------------------------------------
	// phrase management
	//------------------------------------------------------------------------
	
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

	//------------------------------------------------------------------------
	// sense management
	//------------------------------------------------------------------------
	
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
	
	//------------------------------------------------------------------------
	// comment management
	//------------------------------------------------------------------------
	
	function set_comment($comment = ''){
		$comment = new Comment($this->dictionary);
		$this->comment = $comment;
		
		return $comment;
	}
	
	function get_comment(){
		return $comment;
	}
	
}

?>
