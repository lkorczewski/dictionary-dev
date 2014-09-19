<?php

use \Dictionary\Dictionary;

class Element_Test extends PHPUnit_Framework_TestCase {
	
	protected $class_name = 'Element';
	protected $snake_case_name  = '';
	protected $camel_case_name  = '';
	
	protected $element;
	protected $dictionary;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		
		$class_name = '\\Dictionary\\' . $this->class_name;
		if((new ReflectionClass($class_name))->isAbstract()){
			$this->element = $this->getMockForAbstractClass($class_name, [$this->dictionary]);
		} else {
			$this->element = new $class_name($this->dictionary);
		}
	}
	
	function test_snake_case_name(){
		$this->assertEquals(
			$this->element->get_snakized_name(),
			$this->snake_case_name
		);
	}
	
	function test_camel_case_name(){
		$this->assertEquals(
			$this->element->get_camelized_name(),
			$this->camel_case_name
		);
	}
	
}