<?php

//========================================================
// Abstraction of extended node
//--------------------------------------------------------
// Contains:
//  - node id
//  - forms
//  - translations
//========================================================

namespace Dictionary;

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/node.php';
require_once __DIR__ . '/traits/has_forms.php';
require_once __DIR__ . '/traits/has_category_label.php';

abstract class Headword_Node extends Node
	implements
		Node_With_Category_Label,
		Node_With_Forms
{
	
	use Has_Category_Label;
	use Has_Forms;

	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	public function __construct($dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
	}
	
}

?>
