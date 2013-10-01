<?php

require_once __DIR__.'/dictionary.php';

class Translation {
	private $dictionary;
	
	private $id;	
	private $text;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $text = NULL){
		$this->dictionary = $dictionary;
		if($text) $this->text = $text;
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
	
	function set_text($text){
		$this->text = $text;
	}
	
	function get_text(){
		return $this->text;
	}
	
}

?>
