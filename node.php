<?php

//========================================================
// Abstraction of node
//--------------------------------------------------------
// Contains:
//  - node id
//  - translations
//========================================================

namespace Dictionary;

require_once __DIR__ . '/element.php';

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/traits/has_translations.php';

abstract class Node extends Element
	implements Node_With_Translations
{
	
	use Has_Translations;
	
	private $id;
	private $node_id;
	
	//------------------------------------------------------------------------
	// id management
	//------------------------------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
		
		return $this;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// node id management
	//------------------------------------------------
	
	function set_node_id($node_id){
		$this->node_id = $node_id;
		
		return $this;
	}
	
	function get_node_id(){
		return $this->node_id;
	}
	
}
