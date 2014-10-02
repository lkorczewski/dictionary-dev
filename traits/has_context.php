<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Context extends Node_Interface {
	function set_context();
	function get_context();
}

//----------------------------------------------------------------------------

require_once __DIR__ . '/../context.php';

trait Has_Context {
	
	private $context;
	
	//------------------------------------------------
	// context management
	//------------------------------------------------
	
	function set_context($context = null){
		if(!$context instanceof Context){
			$context = new Context($this->dictionary, $context);
		}
		$this->context = $context;
		
		return $context;
	}
	
	function get_context(){
		return $this->context;
	}
	
}
