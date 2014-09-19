<?php

require_once __DIR__ . '/element_test.php';

use \Dictionary\Sense;

class Sense_Test extends Element_Test {
	
	protected $class_name       = 'Sense';
	protected $snake_case_name  = 'sense';
	protected $camel_case_name  = 'Sense';
	
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
	
	function test_label(){
		$label = 'test';
		$this->element->set_label($label);
		$this->assertEquals($this->element->get_label(), $label);
	}
	
	function test_senses(){
		$sense_1 = $this->element->add_sense();
		$sense_2 = $this->element->add_sense();
		$this->assertInstanceOf('\Dictionary\Sense', $sense_1);
		$this->assertInstanceOf('\Dictionary\Sense', $sense_2);
		$this->assertSame($this->element->get_sense(), $sense_1);
		$this->assertSame($this->element->get_sense(), $sense_2);
		$this->assertEquals($this->element->get_sense(), false);
		$this->assertNotSame($sense_1, $sense_2);
		
	}
	
	function test_translations(){
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
