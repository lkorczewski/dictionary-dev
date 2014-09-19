<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';

class Phrase extends Node {
	
	protected static $snakized_name     = 'phrase';
	protected static $camelized_name    = 'Phrase';
	
	private $phrase;
	
	//------------------------------------------------
	// phrase management
	//------------------------------------------------
	
	function set($phrase){
		$this->phrase = $phrase;
		
		return $this;
	}
	
	function get(){
		return $this->phrase;
	}
	
}

