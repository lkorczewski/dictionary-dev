<?php

//========================================================
// Abstraction of extended node
//--------------------------------------------------------
// Contains:
//  - node id
//  - forms
//  - translations
//========================================================

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';
require_once __DIR__.'/form.php';
require_once __DIR__.'/category_label.php';

abstract class Headword_Node extends Node {
	
	private $category_label;
	
	private $forms;
	private $form_iterator;

	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct($dictionary){
		parent::__construct($dictionary);
		
		$this->dictionary = $dictionary;
		
		$this->category_label = false;
		
		$this->forms = array();
		$this->form_iterator = 0;
		
	}

	//------------------------------------------------
	// category label management
	//------------------------------------------------
	
	function set_category_label(){
		$category_label = new Category_Label($this->dictionary);
		$this->category_label = $category_label;
		
		return $category_label;
	}
	
	function get_category_label(){
		$category_label = $this->category_label;
		
		return $category_label;
	}

	//------------------------------------------------
	// form management
	//------------------------------------------------
	
	function add_form(){
		$form = new Form($this->dictionary);
		$this->forms[] = $form;
		
		return $form;
	}
	
	function get_form(){
		if(!isset($this->forms[$this->form_iterator])) return false;
		
		$form = $this->forms[$this->form_iterator];
		$this->form_iterator++;
		
		return $form;
	}

}

?>
