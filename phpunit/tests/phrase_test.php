<?php

use \Dictionary\Dictionary;
use \Dictionary\Phrase;

class Phrase_Test extends PHPUnit_Framework_TestCase {

	protected $dictionary;
	protected $phrase;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		$this->phrase = new Phrase($this->dictionary);
	}
	
	function test_id(){
		$id = 4;
		$this->phrase->set_id($id);
		$this->assertEquals($this->phrase->get_id(), $id);
	}
	
	function test_value(){
		$value = 'test';
		$this->phrase->set($value);
		$this->assertEquals($this->phrase->get(), $value);
	}
	
	function test_translation(){
		$this->phrase->add_translation();
		$this->assertInstanceOf('Dictionary\Translation', $this->phrase->get_translation());
	}
	
	function test_node_id(){
		$node_id = 159;
		$this->phrase->set_node_id($node_id);
		$this->assertSame($this->phrase->get_node_id(), $node_id);
	}
}
