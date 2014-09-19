<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Sense;

class Sense_Test extends Node_Test {
	
	protected $class_name       = 'Sense';
	protected $snake_case_name  = 'sense';
	protected $camel_case_name  = 'Sense';
	
	//------------------------------------------------
	// children
	//------------------------------------------------
	
	function test_label(){
		$label = 'test';
		$this->element->set_label($label);
		$this->assertEquals($this->element->get_label(), $label);
	}
	
	function test_category_label(){
		$this->_test_single_child('category_label');
	}
	
	function test_forms(){
		$this->_test_collection('form');
	}
	
	function test_context(){
		$this->_test_single_child('context');
	}
	
	function test_translations(){
		$this->_test_collection('translation');
	}
	
	function test_phrases(){
		$this->_test_collection('phrase');
	}
	
	function test_senses(){
		$this->_test_collection('sense');
	}
	
}
