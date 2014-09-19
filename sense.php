<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/headword_node.php';

require_once __DIR__ . '/context.php';
require_once __DIR__ . '/phrase.php';

require_once __DIR__ . '/traits/has_context.php';
require_once __DIR__ . '/traits/has_phrases.php';
require_once __DIR__ . '/traits/has_senses.php';

class Sense extends Headword_Node
	implements
		Node_With_Context,
		Node_With_Phrases,
		Node_With_Senses
{
	
	protected static $snakized_name   = 'sense';
	protected static $camelized_name  = 'Sense';
	
	private $label;
	
	use Has_Context;
	use Has_Phrases;
	use Has_Senses;
	
	//------------------------------------------------
	// label management
	//------------------------------------------------
	
	function set_label($label){
		$this->label = $label;
		
		return $this;
	}
	
	function get_label(){
		return $this->label;
	}
	
}

