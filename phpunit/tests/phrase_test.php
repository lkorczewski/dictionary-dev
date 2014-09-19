<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Dictionary;
use \Dictionary\Phrase;

class Phrase_Test extends Element_Test {
	
	protected $class_name       = 'Phrase';
	protected $snake_case_name  = 'phrase';
	protected $camel_case_name  = 'Phrase';
	
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
	
	function test_value(){
		$value = 'test';
		$this->element->set($value);
		$this->assertEquals($this->element->get(), $value);
	}
	
	function test_translation(){
		$translation_1 = $this->element->add_translation();
		$translation_2 = $this->element->add_translation();
		$this->assertInstanceOf('\Dictionary\Translation', $translation_1);
		$this->assertInstanceOf('\Dictionary\Translation', $translation_2);
		$this->assertSame($this->element->get_translation(), $translation_1);
		$this->assertSame($this->element->get_translation(), $translation_2);
		$this->assertEquals($this->element->get_translation(), false);
		$this->assertNotSame($translation_1, $translation_2);
	}
	
}
