<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/headword_node.php';

require_once __DIR__ . '/traits/has_headwords.php';
require_once __DIR__ . '/traits/has_pronunciations.php';
require_once __DIR__ . '/traits/has_phrases.php';
require_once __DIR__ . '/traits/has_senses.php';

class Entry extends Headword_Node
	implements
		Node_With_Headwords,
		Node_With_Pronunciations,
		Node_With_Phrases,
		Node_With_Senses
{
	
	protected static $snakized_name   = 'entry';
	protected static $camelized_name  = 'Entry';
	
	private $id;
	
	use Has_Headwords;
	use Has_Pronunciations;
	use Has_Phrases;
	use Has_Senses;
	
	//------------------------------------------------------------------------
	// comment management
	//------------------------------------------------------------------------
	/*
	function set_comment($comment = ''){
		$comment = new Comment($this->dictionary);
		$this->comment = $comment;
		
		return $comment;
	}
	
	function get_comment(){
		return $comment;
	}
	*/
	
}
