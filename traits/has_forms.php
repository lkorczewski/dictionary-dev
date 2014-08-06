<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Forms extends Node_Interface {
	public function add_form();
	public function get_form();
}

//----------------------------------------------------------------------------

require_once __DIR__ . '/../form.php';

trait Has_Forms {
	private $forms = [];
	private $form_iterator = 0;
	
	//------------------------------------------------
	// form management
	//------------------------------------------------
	
	public function add_form(){
		$form = new Form($this->dictionary);
		$this->forms[] = $form;
		
		return $form;
	}
	
	public function get_form(){
		if(!isset($this->forms[$this->form_iterator])) return false;
		
		$form = $this->forms[$this->form_iterator];
		$this->form_iterator++;
		
		return $form;
	}
	
}

