<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__.'/node_interface.php';

interface Node_With_Pronunciations extends Node_Interface {
	public function add_pronunciation();
	public function get_pronunciation();
}

//----------------------------------------------------------------------------

require_once __DIR__.'/../pronunciation.php';

trait Has_Pronunciations {
	
	private $pronunciations = array();
	private $pronunciation_iterator = 0;
	
	//------------------------------------------------------------------------
	// pronunciation management
	//------------------------------------------------------------------------
	
	public function add_pronunciation(){
		$pronunciation = new Pronunciation($this->dictionary);
		$this->pronunciations[] = $pronunciation;
		
		return $pronunciation;
	}
	
	public function get_pronunciation(){
		if(!isset($this->pronunciations[$this->pronunciation_iterator])){
			return false;
		}
		
		$pronunciation = $this->pronunciations[$this->pronunciation_iterator];
		$this->pronunciation_iterator++;
		
		return $pronunciation;
	}
	
}

?>
