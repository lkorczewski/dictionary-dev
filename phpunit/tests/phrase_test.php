<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Dictionary;
use \Dictionary\Phrase;

class Phrase_Test extends Node_Test {
	
	protected $class_name       = 'Phrase';
	protected $snake_case_name  = 'phrase';
	protected $camel_case_name  = 'Phrase';
	
	function test_value(){
		$value = 'test';
		$this->element->set($value);
		$this->assertEquals($this->element->get(), $value);
	}
	
	function test_translations(){
		$this->_test_collection('translation');
	}
	
}
