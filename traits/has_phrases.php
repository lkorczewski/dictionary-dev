<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Phrases extends Node_Interface {
	public function add_phrase();
	public function get_phrase();
}

//----------------------------------------------------------------------------

require_once __DIR__ . '/../phrase.php';

trait Has_Phrases {
	
	private $phrases = [];
	private $phrase_iterator = 0;
	
	//------------------------------------------------------------------------
	// phrase management
	//------------------------------------------------------------------------
	
	public function add_phrase(){
		$phrase = new Phrase($this->dictionary);
		$this->phrases[] = $phrase;
		
		return $phrase;
	}
	
	public function get_phrase(){
		if(!isset($this->phrases[$this->phrase_iterator])){
			return false;
		}
		
		$phrase = $this->phrases[$this->phrase_iterator];
		$this->phrase_iterator++;
		
		return $phrase;
	}
	
}

?>
