<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Entry;

class Entry_Test extends Element_Test {
	
	protected $class_name       = 'Entry';
	protected $snake_case_name  = 'entry';
	protected $camel_case_name  = 'Entry';
	
	function test_id(){
		$id = 4;
		$this->element->set_id($id);
		$this->assertEquals($this->element->get_id(), $id);
	}
	
	function test_node_id(){
		$node_id = 159;
		$this->element->set_node_id($node_id);
		$this->assertEquals($this->element->get_node_id(), $node_id);
	}
	
	function test_headwords(){
		$this->_test_collection('headword');
	}
	
	function test_pronuntiations(){
		$this->_test_collection('pronunciation');
	}
	
	function test_category_label(){
		$category_label = $this->element->set_category_label();
		$this->assertInstanceOf('\Dictionary\Category_Label', $category_label);
		$this->assertEquals($this->element->get_category_label(), $category_label);
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
	
	protected function _test_collection($name){
		$add_method = "add_$name";
		$get_method = "get_$name";
		$element_1 = $this->element->$add_method();
		$element_2 = $this->element->$add_method();
		$this->assertInstanceOf('\Dictionary\\' . $this->make_class_name($name), $element_1);
		$this->assertInstanceOf('\Dictionary\\' . $this->make_class_name($name), $element_2);
		$this->assertSame($this->element->$get_method(), $element_1);
		$this->assertSame($this->element->$get_method(), $element_2);
		$this->assertEquals($this->element->$get_method(), false);
		$this->assertNotSame($element_1, $element_2);
	}
	
	protected function make_class_name($name){
		$segments = explode('_', $name);
		$segments = array_map('ucfirst', $segments);
		return implode('_', $segments);
	}
	
}