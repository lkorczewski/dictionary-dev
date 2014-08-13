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
	
	public function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	public function set_id($id){
		$this->id = $id;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// phrase management
	//------------------------------------------------
	
	public function set($phrase){
		$this->phrase = $phrase;
	}
	
	public function get(){
		return $this->phrase;
	}
	
}
