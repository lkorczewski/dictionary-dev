<?php

use \Dictionary\Dictionary;
use \Dictionary\Sense;

class Sense_Test extends PHPUnit_Framework_TestCase {
	protected $dictionary;
	protected $sense;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		$this->sense = new Sense($this->dictionary);
	}
	
	function test_id(){
		$id = 4;
		$this->sense->set_id($id);
		$this->assertEquals($this->sense->get_id(), $id);
	}
	
	function test_node_id(){
		$node_id = 159;
		$this->sense->set_node_id($node_id);
		$this->assertEquals($this->sense->get_node_id(), $node_id);
	}
	
	function test_label(){
		$label = 'test';
		$this->sense->set_label($label);
		$this->assertEquals($this->sense->get_label(), $label);
	}
	
	function test_senses(){
		$sense_1 = $this->sense->add_sense();
		$sense_2 = $this->sense->add_sense();
		$this->assertInstanceOf('\Dictionary\Sense', $sense_1);
		$this->assertInstanceOf('\Dictionary\Sense', $sense_2);
		$this->assertSame($this->sense->get_sense(), $sense_1);
		$this->assertSame($this->sense->get_sense(), $sense_2);
		$this->assertEquals($this->sense->get_sense(), false);
		$this->assertNotSame($sense_1, $sense_2);
		
	}
	
	function test_translations(){
		$translation_1 = $this->sense->add_translation();
		$translation_2 = $this->sense->add_translation();
		$this->assertInstanceOf('\Dictionary\Translation', $translation_1);
		$this->assertInstanceOf('\Dictionary\Translation', $translation_2);
		$this->assertSame($this->sense->get_translation(), $translation_1);
		$this->assertSame($this->sense->get_translation(), $translation_2);
		$this->assertEquals($this->sense->get_translation(), false);
		$this->assertNotSame($translation_1, $translation_2);
	}
	
}
