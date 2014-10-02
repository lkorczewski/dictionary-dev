<?php

require_once __DIR__ . '/node_test.php';

use \Dictionary\Entry;

class Entry_Test extends Node_Test {
	
	protected $class_name       = 'Entry';
	protected $snake_case_name  = 'entry';
	protected $camel_case_name  = 'Entry';
	
	//------------------------------------------------
	// children
	//------------------------------------------------
	
	function test_headwords(){
		$this->_test_collection('headword');
	}
	
	function test_pronunciations(){
		$this->_test_collection('pronunciation');
	}
	
	function test_category_label(){
		$this->_test_single_child('category_label');
	}
	
	function test_forms(){
		$this->_test_collection('phrase');
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
	
}
