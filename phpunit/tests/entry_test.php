<?php

use \Dictionary\Dictionary;
use \Dictionary\Entry;

class Entry_Test extends PHPUnit_Framework_TestCase {
	protected $dictionary;
	protected $entry;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		$this->entry = new Entry($this->dictionary);
	}
	
	function test_id(){
		$id = 4;
		$this->entry->set_id($id);
		$this->assertEquals($this->entry->get_id(), $id);
	}
	
	function test_node_id(){
		$node_id = 159;
		$this->entry->set_node_id($node_id);
		$this->assertEquals($this->entry->get_node_id(), $node_id);
	}
	
	function test_headwords(){
		$this->_test_collection('headword');
	}
	
	function test_pronuntiations(){
		$this->_test_collection('pronunciation');
	}
	
	function test_category_label(){
		$category_label = $this->entry->set_category_label();
		$this->assertInstanceOf('\Dictionary\Category_Label', $category_label);
		$this->assertEquals($this->entry->get_category_label(), $category_label);
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
		$element_1 = $this->entry->$add_method();
		$element_2 = $this->entry->$add_method();
		$this->assertInstanceOf('\Dictionary\\' . $this->make_class_name($name), $element_1);
		$this->assertInstanceOf('\Dictionary\\' . $this->make_class_name($name), $element_2);
		$this->assertSame($this->entry->$get_method(), $element_1);
		$this->assertSame($this->entry->$get_method(), $element_2);
		$this->assertEquals($this->entry->$get_method(), false);
		$this->assertNotSame($element_1, $element_2);
	}
	
	protected function make_class_name($name){
		$segments = explode('_', $name);
		$segments = array_map('ucfirst', $segments);
		return implode('_', $segments);
	}
	
}