<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Senses extends Node_Interface {
	function add_sense();
	function get_sense();
	function get_senses();
}

//----------------------------------------------------------------------------

// require_once __DIR__ . '/../sense.php';

trait Has_Senses {
	
	private $senses = [];
	private $sense_iterator = 0;
	
	//------------------------------------------------------------------------
	// sense management
	//------------------------------------------------------------------------
	
	function add_sense(){
		$sense = new Sense($this->dictionary);
		$this->senses[] = $sense;
		
		return $sense;
	}
	
	function get_sense(){
		if(!isset($this->senses[$this->sense_iterator])){
			$this->sense_iterator = 0;
			return false;
		}
		
		$sense = $this->senses[$this->sense_iterator];
		$this->sense_iterator++;
		
		return $sense;
	}

	function get_senses(){
		return $this->senses;
	}
	
}
