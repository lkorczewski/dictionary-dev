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
	
	protected $dictionary;
	
	private $node_id;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		
		$this->dictionary = $dictionary;
		
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

