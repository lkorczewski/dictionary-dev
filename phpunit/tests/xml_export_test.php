<?php

require_once __DIR__ . '/../../layouts/XML_layout.php';

use Dictionary\Entry;
use Dictionary\Sense;
use Dictionary\Phrase;
use Dictionary\Headword;
use Dictionary\Pronunciation;
use Dictionary\Category_Label;
use Dictionary\Form;
use Dictionary\Translation;

use Dictionary\XML_Layout;

use \ReflectionClass;

class XML_Layout_Test extends PHPUnit_Framework_TestCase {
	
	protected $xml_layout;
	protected $data;
	protected $dictionary;
	
	function setup(){
		$this->xml_layout = new XML_Layout();

		$this->data        = $this->getMock('Dictionary\Data');
		$this->dictionary  = $this->getMock('Dictionary\Dictionary', [], [$this->data]);
	}
	
	function test_simple_entry(){
		$entry = new Entry($this->dictionary);
		$expected_string =
			"<Entry>\n" .
			"</Entry>\n";

		$this->_test_element($entry, $expected_string);
	}

	// should it be allowed?
	function test_simple_sense(){
		$sense = new Sense($this->dictionary);
		$expected_string =
			"<Sense>\n" .
			"</Sense>\n";

		$this->_test_element($sense, $expected_string);
	}
	
	function test_sense_with_label(){
		$sense = (new Sense($this->dictionary))
			->set_label('label');
		$expected_string =
			"<Sense>\n" .
			" <L>label</L>\n" .
			"</Sense>\n";

		$this->_test_element($sense, $expected_string);
	}

	function test_simple_phrase(){
		$phrase = new Phrase($this->dictionary);
		$expected_string =
			"<Phrase>\n" .
			" <H></H>\n" .
			"</Phrase>\n";

		$this->_test_element($phrase, $expected_string);
	}

	function test_full_phrase(){
		$phrase = new Phrase($this->dictionary);
		$phrase->set('test phrase');
		$phrase->add_translation()->set('test translation 1');
		$phrase->add_translation()->set('test translation 2');
		$expected_string =
			"<Phrase>\n" .
			" <H>test phrase</H>\n" .
			" <T>test translation 1</T>\n" .
			" <T>test translation 2</T>\n" .
			"</Phrase>\n";

		$this->_test_element($phrase, $expected_string);
	}

	function test_headword(){
		$headword = new Headword($this->dictionary, 'test value');
		$expected_string = "<H>test value</H>\n";

		$this->_test_element($headword, $expected_string);
	}

	function test_pronunciation(){
		$pronunciation = new Pronunciation($this->dictionary, 'test value');
		$expected_string = "<P>test value</P>\n";

		$this->_test_element($pronunciation, $expected_string);
	}

	function test_category_label(){
		$category_label = new Category_Label($this->dictionary, 'test value');
		$expected_string = "<CL>test value</CL>\n";

		$this->_test_element($category_label, $expected_string);
	}

	/*
	// not allowed
	function test_simple_form(){
		$form = (new Form($this->dictionary))
			->set_form('test form');
		$expected_string =
			"<Form>\n" .
			" <H>test form</H>\n" .
			"</Form>\n";

		$this->_test_element($form, $expected_string);
	}
	*/

	function test_labelled_form(){
		$form = (new Form($this->dictionary))
			->set_label('test form label')
			->set_form('test form');
		$expected_string =
			"<Form>\n" .
			" <L>test form label</L>\n" .
			" <H>test form</H>\n" .
			"</Form>\n";

		$this->_test_element($form, $expected_string);
	}
	
	function test_translation(){
		$translation = new Translation($this->dictionary, 'test value');
		$expected_string = "<T>test value</T>\n";

		$this->_test_element($translation, $expected_string);
	}
	
	protected function _test_element($instance, $expected_string){
		
		$name = strtolower((new ReflectionClass($instance))->getShortName());

		$parsed_string = $this->xml_layout->{'parse_'.$name}($instance);
		$this->assertEquals($parsed_string, $expected_string);

		$parsed_string = $this->xml_layout->parse($instance);
		$this->assertEquals($parsed_string, $expected_string);
	}
	
}
