<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Form;

class Form_Test extends Element_Test {
	
	protected $class_name       = 'Form';
	protected $snake_case_name  = 'form';
	protected $camel_case_name  = 'Form';
	
	function test_id(){
		$id = 4;
		$this->element->set_id($id);
		$this->assertEquals($this->element->get_id(), $id);
	}
	
	function test_label(){
		$label = 'test';
		$this->element->set_label($label);
		$this->assertEquals($this->element->get_label(), $label);
	}
	
	function test_form(){
		$form = 'test';
		$this->element->set_form($form);
		$this->assertEquals($this->element->get_form(), $form);
	}
	
}

