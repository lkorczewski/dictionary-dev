<?php

namespace Dictionary;

require_once __DIR__ . '/element.php';

require_once __DIR__ . '/dictionary.php';

class Form extends Element {
	private $dictionary;
	
	protected static $snakized_name   = 'form';
	protected static $camelized_name  = 'Form';
	
	private $id;
	private $label;
	private $form;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary, $label = null, $form = null){
		$this->dictionary = $dictionary;
		
		if($label) $this->label = $label;
		if($form) $this->form = $form;
	}
	
	//------------------------------------------------
	// id management
	//------------------------------------------------
	
	function set_id($id){
		$this->id = $id;
		
		return $this;
	}
	
	function get_id(){
		return $this->id;
	}
	
	//------------------------------------------------
	// managing label
	//------------------------------------------------
	
	function set_label($label){
		$this->label = $label;
		
		return $this;
	}
	
	function get_label(){
		return $this->label;
	}
	
	//------------------------------------------------
	// managing form
	//------------------------------------------------
	
	function set_form($form){
		$this->form = $form;
		
		return $this;
	}
	
	function get_form(){
		return $this->form;
	}
	
	// temporary hack
	function set($form){
		return $this->set_form($form);
	}
	
	// temporary hack
	function get(){
		return $this->get_form();
	}
}

