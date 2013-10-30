<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/headword_node.php';

require_once __DIR__ . '/context.php';
require_once __DIR__ . '/phrase.php';

require_once __DIR__ . '/traits/has_phrases.php';
require_once __DIR__ . '/traits/has_senses.php';

class Sense extends Headword_Node
  implements
    Has_Phrases_Interface,
    Has_Senses_Interface
{
	
	private $id;
	
	private $label;
	
	private $context;
	
	use Has_Phrases_Trait;
	use Has_Senses_Trait;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// label management
	//------------------------------------------------
	
	function set_label($label){
		$this->label = $label;
	}
	
	function get_label(){
		return $this->label;
	}
	
	//------------------------------------------------
	// context management
	//------------------------------------------------
	
	function set_context(){
		$context = new Context($this->dictionary);
		$this->context = $context;
		
		return $context;		
	}
	
	function get_context(){
		return $this->context;
	}
	
}

?>
