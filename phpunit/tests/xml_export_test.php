<?php

require_once __DIR__ . '/../../layouts/XML_layout.php';

use Dictionary\Dictionary;
use Dictionary\Entry;
use Dictionary\Sense;
use Dictionary\Headword;
use Dictionary\Translation;
use Dictionary\XML_Layout;

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
		$expected_string =  "<Entry>\n</Entry>\n";
		$entry = new Entry($this->dictionary);
		
		$parsed_string = $this->xml_layout->parse_entry($entry);
		$this->assertEquals($parsed_string, $expected_string);
		
		$parsed_string = $this->xml_layout->parse($entry);
		$this->assertEquals($parsed_string, $expected_string);
	}
	
	function test_simple_sense(){
		$expected_string = "<Sense>\n<Sense>\n";		
		$sense = new Sense($this->dictionary);
		
		$parsed_string = $this->xml_layout->parse_sense($sense);
		$this->assertEquals($parsed_string, $expected_string);
		
		$parsed_string = $this->xml_layout->parse($sense);
		$this->assertEquals($parsed_string, $expected_string);
	}
	

	function test_parsing_headword(){
		$expected_string = "<H>test value</H>\n";
		$headword = new Headword($this->dictionary, 'test value');
		
		$parsed_string = $this->xml_layout->parse_headword($headword);
		$this->assertEquals($parsed_string, $expected_string);
		
		$parsed_string = $this->xml_layout->parse($headword);
		$this->assertEquals($parsed_string, $expected_string);
	}
	
	function test_parsing_tranlsation(){
		$translation = new Translation($this->dictionary, 'test value');
		
		$parsed_string = $this->xml_layout->parse_translation($translation);
		$this->assertEquals($parsed_string, "<T>test value</T>\n");

		$parsed_string = $this->xml_layout->parse($translation);
		$this->assertEquals($parsed_string, "<T>test value</T>\n");
	}
	
}
