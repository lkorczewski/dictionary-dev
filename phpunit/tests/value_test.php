<?php

use \Dictionary\Dictionary;
use \Dictionary\Value;

class Value_Test extends PHPUnit_Framework_TestCase {
	
	protected $value_name = 'Value';
	
	protected $dictionary;
	protected $value;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		
		$class_name = '\\Dictionary\\' . $this->value_name;
		if($this->value_name == 'Value'){
			$this->value = $this->getMockForAbstractClass($class_name, [$this->dictionary]);
		} else {
			$this->value = new $class_name($this->dictionary);
		}
	}
	
	function test_value_id(){
		$id = 4;
		$this->value->set_id($id);
		$this->assertEquals($this->value->get_id(), $id);
	}
	
	function test_value_value(){
		$value = 'test';
		$this->value->set($value);
		$this->assertEquals($this->value->get(), $value);
	}
	
}

