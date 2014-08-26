<?php

require_once __DIR__ . '/../../../layouts/table_layout.php';

use Dictionary\Entry;
use Dictionary\Sense;
use Dictionary\Phrase;
use Dictionary\Headword;
use Dictionary\Pronunciation;
use Dictionary\Category_Label;
use Dictionary\Form;
use Dictionary\Context;
use Dictionary\Translation;

use Dictionary\Table_Layout;

use \ReflectionClass;

class Table_Layout_Test extends PHPUnit_Framework_TestCase {
	
	protected $xml_layout;
	protected $data;
	protected $dictionary;
	
	function setup(){
		$this->xml_layout = new Table_Layout();

		$this->data        = $this->getMock('Dictionary\Data');
		$this->dictionary  = $this->getMock('Dictionary\Dictionary', [], [$this->data]);
	}
	
	//--------------------------------------
	// entry
	//--------------------------------------
	
	function test_simple_entry(){
		$entry = new Entry($this->dictionary);
		$expected_array = [];
		
		$this->_test_element($entry, $expected_array);
	}
	
	function test_full_entry(){
		$entry = new Entry($this->dictionary);
		$entry->add_headword('headword 1');
		$entry->add_headword('headword 2');
		$entry->add_pronunciation('pronunciation 1');
		$entry->add_pronunciation('pronunciation 2');
		$entry->set_category_label('test category label');
		$entry->add_form()->set_label('form label 1')->set_form('form 1');
		$entry->add_form()->set_label('form label 2')->set_form('form 2');
		$entry->add_translation('translation 1');
		$entry->add_translation('translation 2');
		$entry->add_phrase()->set('phrase 1');
		$entry->add_phrase()->set('phrase 2');
		$entry->add_sense()->set_label('sense 1');
		$entry->add_sense()->set_label('sense 2');
		$expected_array = [
			'headwords' => [
				'headword 1',
				'headword 2',
			],
			'pronunciations' => [
				'pronunciation 1',
				'pronunciation 2',
			],
			'category_label'  => 'test category label',
			'forms' => [
				[
					'label'     => 'form label 1',
					'headword'  => 'form 1',
				],
				[
					'label'     => 'form label 2',
					'headword'  => 'form 2',
				],
			],
			'translations' => [
				'translation 1',
				'translation 2',
			],
			'phrases' => [
				[ 'headword'  => 'phrase 1' ],
 				[ 'headword'  => 'phrase 2' ],
			],
			'senses' => [
				[ 'label'  => 'sense 1' ],
				[ 'label'  => 'sense 2' ],
			],
		];
		
		$this->_test_element($entry, $expected_array);
	}
	
	//--------------------------------------
	// sense
	//--------------------------------------
	
	// should it be allowed?
	function test_simple_sense(){
		$sense = new Sense($this->dictionary);
		$expected_array = [
			'label' => '',
		];
		
		$this->_test_element($sense, $expected_array);
	}
	
	function test_sense_with_label(){
		$sense = (new Sense($this->dictionary))
			->set_label('label');
		$expected_array = [
			'label' => 'label'
		];
		
		$this->_test_element($sense, $expected_array);
	}
	
	function test_full_sense(){
		$sense = new Sense($this->dictionary);
		$sense->set_label('test label');
		$sense->set_category_label('test category label');
		$sense->add_form()->set_label('form label 1')->set_form('form 1');
		$sense->add_form()->set_label('form label 2')->set_form('form 2');
		$sense->set_context('context');
		$sense->add_translation('translation 1');
		$sense->add_translation('translation 2');
		$sense->add_phrase()->set('phrase 1');
		$sense->add_phrase()->set('phrase 2');
		$sense->add_sense()->set_label('sense 1');
		$sense->add_sense()->set_label('sense 2');
		$expected_array = [
			'label'           => 'test label',
			'category_label'  => 'test category label',
			'forms' => [
				[
					'label'     => 'form label 1',
					'headword'  => 'form 1',
				],
				[
					'label'     => 'form label 2',
					'headword'  => 'form 2',
				],
			],
			'context'         => 'context',
			'translations'    => [
				'translation 1',
				'translation 2',
			],
			'phrases'         => [
				[ 'headword'  => 'phrase 1' ],
				[ 'headword'  => 'phrase 2' ],
			],
			'senses'          => [
				[ 'label'  => 'sense 1' ],
				[ 'label'  => 'sense 2' ],
			],
		];
		
		$this->_test_element($sense, $expected_array);
	}
	
	//--------------------------------------
	// phrase
	//--------------------------------------
	
	function test_simple_phrase(){
		$phrase = new Phrase($this->dictionary);
		$expected_array = [
			'headword'  => '',
		];
		
		$this->_test_element($phrase, $expected_array);
	}
	
	function test_full_phrase(){
		$phrase = new Phrase($this->dictionary);
		$phrase->set('phrase');
		$phrase->add_translation('translation 1');
		$phrase->add_translation('translation 2');
		$expected_string = [
			'headword'      => 'phrase',
			'translations'  => [
				'translation 1',
				'translation 2',
			],
		];
		
		$this->_test_element($phrase, $expected_string);
	}
	
	//--------------------------------------
	// headword
	//--------------------------------------
	
	function test_headword(){
		$headword = new Headword($this->dictionary, 'test headword');
		$expected_string = 'test headword';
		
		$this->_test_element($headword, $expected_string);
	}
	
	//--------------------------------------
	// pronunciation
	//--------------------------------------
	
	function test_pronunciation(){
		$pronunciation = new Pronunciation($this->dictionary, 'test pronunciation');
		$expected_string = 'test pronunciation';
		
		$this->_test_element($pronunciation, $expected_string);
	}
	
	//--------------------------------------
	// category label
	//--------------------------------------
	
	function test_category_label(){
		$category_label = new Category_Label($this->dictionary, 'test category label');
		$expected_string = 'test category label';
		
		$this->_test_element($category_label, $expected_string);
	}
	
//	/*
//	// not allowed
//	function test_simple_form(){
//		$form = (new Form($this->dictionary))
//			->set_form('test form');
//		$expected_string =
//			"<Form>\n" .
//			" <H>test form</H>\n" .
//			"</Form>\n";
//			
//		$this->_test_element($form, $expected_string);
//	}
//	*/
	
	//--------------------------------------
	// form
	//--------------------------------------
	
	function test_labelled_form(){
		$form = (new Form($this->dictionary))
			->set_label('test form label')
			->set_form('test form');
		$expected_array = [
			'label'     => 'test form label',
			'headword'  => 'test form',
		];
		
		$this->_test_element($form, $expected_array);
	}
	
	//--------------------------------------
	// context
	//--------------------------------------
	
	function test_context(){
		$context = new Context($this->dictionary, 'test context');
		$expected_string = 'test context';
		
		$this->_test_element($context, $expected_string);
	}
	
	//--------------------------------------
	// translation
	//--------------------------------------
	
	function test_translation(){
		$translation = new Translation($this->dictionary, 'test translation');
		$expected_string = 'test translation';
		
		$this->_test_element($translation, $expected_string);
	}
	
	//--------------------------------------
	// generic element test
	//--------------------------------------
	
	protected function _test_element($instance, $expected_value){
		
		$name = strtolower((new ReflectionClass($instance))->getShortName());
		
		$parsed_value = $this->xml_layout->{'parse_'.$name}($instance);
		$this->assertEquals($parsed_value, $expected_value);
		
		/*
		$parsed_array = $this->xml_layout->parse($instance);
		$this->assertEquals($parsed_array, $expected_array);
		*/
	}
	
}
