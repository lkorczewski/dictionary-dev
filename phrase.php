<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';

class Phrase extends Node {
	
	private $id;
	
	private $phrase;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
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
	// phrase management
	//------------------------------------------------
	
	function set($phrase){
		$this->phrase = $phrase;
	}
	
	function get(){
		return $this->phrase;
	}
	
}

?>
