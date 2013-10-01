<?php

require_once __DIR__ . '/dictionary.php';

class Comment {
	private $dictionary;
	
	private $id;
	private $comment;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $comment = NULL){
		$this->dictionary = $dictionary;
		
		if($comment) $this->comment = $comment;
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
	// setting value
	//------------------------------------------------
	
	function set($comment){
		$this->comment = $comment;
	}
	
	function get(){
		return $this->comment;
	}
	
}

?>
