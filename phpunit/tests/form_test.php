<?php

use \Dictionary\Dictionary;
use \Dictionary\Form;

class Form_Test extends PHPUnit_Framework_TestCase {
	
	protected $dictionary;
	protected $form;
	
	function setup(){
		$data = $this->getMock('\Dictionary\Data');
		
		$this->dictionary = new Dictionary($data);
		$this->form = new Form($this->dictionary);
	}
	
	function test_id(){
		$id = 4;
		$this->form->set_id($id);
		$this->assertEquals($this->form->get_id(), $id);
	}
	
	function test_label(){
		$label = 'test';
		$this->form->set_label($label);
		$this->assertEquals($this->form->get_label(), $label);
	}
	
	function test_form(){
		$form = 'test';
		$this->form->set_form($form);
		$this->assertEquals($this->form->get_form(), $form);
	}
	
}

