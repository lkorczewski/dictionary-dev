<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__.'/node_interface.php';

interface Node_With_Pronunciations extends Node_Interface {
	function add_pronunciation();
	function get_pronunciation();
	function get_pronunciations();
}

//----------------------------------------------------------------------------

require_once __DIR__.'/../pronunciation.php';

trait Has_Pronunciations {
	
	private $pronunciations = [];
	private $pronunciation_iterator = 0;
	
	//------------------------------------------------------------------------
	// pronunciation management
	//------------------------------------------------------------------------
	
	function add_pronunciation(){
		$pronunciation = new Pronunciation($this->dictionary);
		$this->pronunciations[] = $pronunciation;
		
		return $pronunciation;
	}
	
	function get_pronunciation(){
		if(!isset($this->pronunciations[$this->pronunciation_iterator])){
			$this->pronunciation_iterator = 0;
			return false;
		}
		
		$pronunciation = $this->pronunciations[$this->pronunciation_iterator];
		$this->pronunciation_iterator++;
		
		return $pronunciation;
	}

	function get_pronunciations(){
		return $this->pronunciations;
	}
	
}
