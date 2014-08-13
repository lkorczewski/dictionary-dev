<?php

//========================================================
// Abstraction of node
//--------------------------------------------------------
// Contains:
//  - node id
//  - translations
//========================================================

namespace Dictionary;

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/traits/has_translations.php';

abstract class Node
	implements Node_With_Translations
{
	
	protected $dictionary;
	
	private $node_id;
	
	use Has_Translations;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	public function __construct(Dictionary $dictionary){
		
		$this->dictionary = $dictionary;
		
	}
	
	//------------------------------------------------
	// node id management
	//------------------------------------------------
	
	public function set_node_id($node_id){
		$this->node_id = $node_id;
	}
	
	function get_node_id(){
		return $this->node_id;
	}
	
}
