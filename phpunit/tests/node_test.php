<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Element;

class Node_Test extends Element_Test {
	
	protected $class_name = 'Node';
	
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
	
	//------------------------------------------------
	// children testing methods
	//------------------------------------------------
	
	protected function _test_single_child($name){
		$set_method = "set_$name";
		$get_method = "get_$name";
		
		$input_element_1 = $this->element->$set_method();
		$input_element_2 = $this->element->$set_method();
		
		$output_element_1 = $this->element->$get_method();
		$output_element_2 = $this->element->$get_method();
		
		$this->assertNotSame($input_element_1, $input_element_2);
		$this->assertSame($output_element_1, $output_element_2);
		
		$class_name = '\Dictionary\\' . $this->make_class_name($name);
		$this->assertInstanceOf($class_name, $output_element_1);
		
	}
	
	protected function _test_collection($name){
		$add_method = "add_$name";
		$get_method = "get_$name";
		
		$input_element_1 = $this->element->$add_method();
		$input_element_2 = $this->element->$add_method();
		
		$output_element_1 = $this->element->$get_method();
		$output_element_2 = $this->element->$get_method();
		$output_element_3 = $this->element->$get_method();
		
		// correct class
		$class_name = '\Dictionary\\' . $this->make_class_name($name);
		$this->assertInstanceOf($class_name, $input_element_1);
		$this->assertInstanceOf($class_name, $input_element_2);
		
		// input = output
		$this->assertSame($output_element_1, $input_element_1);
		$this->assertSame($output_element_2, $input_element_2);
		$this->assertSame($output_element_3, false);
		
		// not the same
		$this->assertNotSame($input_element_1, $input_element_2);
	}
	
	protected function make_class_name($name){
		$segments = explode('_', $name);
		$segments = array_map('ucfirst', $segments);
		return implode('_', $segments);
	}
	
}