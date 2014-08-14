<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Phrases extends Node_Interface {
	function add_phrase();
	function get_phrase();
}

//----------------------------------------------------------------------------

require_once __DIR__ . '/../phrase.php';

trait Has_Phrases {
	
	private $phrases = [];
	private $phrase_iterator = 0;
	
	//------------------------------------------------------------------------
	// phrase management
	//------------------------------------------------------------------------
	
	function add_phrase(){
		$phrase = new Phrase($this->dictionary);
		$this->phrases[] = $phrase;
		
		return $phrase;
	}
	
	function get_phrase(){
		if(!isset($this->phrases[$this->phrase_iterator])){
			$this->phrase_iterator = 0;
			return false;
		}
		
		$phrase = $this->phrases[$this->phrase_iterator];
		$this->phrase_iterator++;
		
		return $phrase;
	}

	function get_phrases(){
		return $this->phrases;
	}
	
}
