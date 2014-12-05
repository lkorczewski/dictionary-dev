<?php

require_once __DIR__ . '/element_test.php';

class Value_Test extends Element_Test {
	
	protected $class_name = 'Value';
	protected $snake_case_name = '';
	protected $camel_case_name = '';
	
	function test_value_id(){
		$id = 4;
		$this->element->set_id($id);
		$this->assertEquals($this->element->get_id(), $id);
	}
	
	function test_value_value(){
		$value = 'test';
		$this->element->set($value);
		$this->assertEquals($this->element->get(), $value);
	}
	
}
