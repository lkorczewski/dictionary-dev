<?php

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/headword_node.php';

require_once __DIR__ . '/traits/has_headwords.php';
require_once __DIR__ . '/traits/has_pronuntiations.php';
require_once __DIR__ . '/traits/has_phrases.php';
require_once __DIR__ . '/traits/has_senses.php';

class Entry
	extends Headword_Node
	implements
		Has_Headwords_Interface,
		Has_Pronuntiations_Interface,
		Has_Phrases_Interface,
		Has_Senses_Interface
{
	
	private $id;
	
	private $headword;
	
	use Has_Headwords_Trait;
	use Has_Pronuntiations_Trait;
	use Has_Phrases_Trait;
	use Has_Senses_Trait;
	
	private $comment;
	
	//------------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
	}
	
	//------------------------------------------------------------------------
	// id management
	//------------------------------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------------------------------
	// comment management
	//------------------------------------------------------------------------
	
	function set_comment($comment = ''){
		$comment = new Comment($this->dictionary);
		$this->comment = $comment;
		
		return $comment;
	}
	
	function get_comment(){
		return $comment;
	}
	
}

?>
