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
		$this->assertEquals($this->form->get_id(), $id);
	}
	
	function test_value(){
		$value = 'test';
		$this->phrase->set($value);
		$this->assertEquals($this->form->get(), $value);
	}
	
	function test_translation(){
		$translation = $this->getMock('\Dictionary\Translation');
		$this->phrase->set_translation($translation);
		$this->phrase->assertSame($this->phrase->get_translation(), $translation);
	}
} 
