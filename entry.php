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
	
	private $id;
	
	use Has_Headwords;
	use Has_Pronunciations;
	use Has_Phrases;
	use Has_Senses;
	
	private $comment;
	
	//------------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------------
	
	public function __construct(Dictionary $dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
	}
	
	//------------------------------------------------------------------------
	// id management
	//------------------------------------------------------------------------
	
	public function set_id($id){
		$this->id = $id;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------------------------------
	// comment management
	//------------------------------------------------------------------------
	
	public function set_comment($comment = ''){
		$comment = new Comment($this->dictionary);
		$this->comment = $comment;
		
		return $comment;
	}
	
	public function get_comment(){
		return $comment;
	}
	
}

?>
