<?php

require_once 'dictionary/dictionary.php';

require_once 'dictionary/entry.php';
require_once 'dictionary/sense.php';
require_once 'dictionary/phrase.php';

require_once 'dictionary/form.php';
require_once 'dictionary/translation.php';

class XML_Layout {
	private $depth;
	private $indent_string;
	
	private $output;

	//--------------------------------------------------------------------
	// konstruktor
	//--------------------------------------------------------------------
	
	function __construct(){
		$this->depth = 0;
		$this->indent_string = ' ';
	}
	
	//--------------------------------------------------------------------
	// depth management
	//--------------------------------------------------------------------
	
	function set_depth($depth){
		$this->depth = $depth;
	}
	
	//--------------------------------------------------------------------
	// indentation
	//--------------------------------------------------------------------
	
	private function set_indent_string($string){
		$this->indent_string = $string;
	}
	
	private function get_indent(){
		return str_repeat($this->indent_string, $this->depth);
	}

	//--------------------------------------------------------------------
	// universal parser
	//--------------------------------------------------------------------
	// just a theoretical fun, no practical purposes expected
	//--------------------------------------------------------------------
	
	function parse($object){
		
		$class_name = get_class($object);
		
		switch($class_name){
			
			case 'Dictionary' :   return $this->parse_dictionary($object);
			
			case 'Entry' :        return $this->parse_entry($object);
			case 'Sense' :        return $this->parse_sense($object);
			case 'Phrase' :       return $this->parse_phrase($object);
			
			case 'Form' :         return $this->parse_form($object);
			case 'Translation' :  return $this->parse_translation($object);
			default :             return false;
			
		}
		
	}
	
	//--------------------------------------------------------------------
	// dictionary parser
	//--------------------------------------------------------------------
	// TO DO: describe behaviour when $sream = false
	//--------------------------------------------------------------------
	
	function parse_dictionary(Dictionary $dictionary, $stream = false){
		$return_content = false;
		
		if($stream === false){
			$return_content = true;
			ob_start();
			$stream = 'php://stdout';
		}
		
		$stream = fopen($stream, 'w');

		fwrite($stream, self::get_indent() . '<Dictionary>'."\n");
		$this->depth++;
		
		$headwords = $dictionary->get_headwords();
		
		foreach($headwords as $headword){
			$entry = $dictionary->get_entry($headword);
			fwrite($stream, $this->parse_entry($entry));
		}
				
		$this->depth--;
		fwrite($stream, self::get_indent() . '</Dictionary>'."\n");
		
		if($return_content){
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
	
	//--------------------------------------------------------------------
	// entry parser
	//--------------------------------------------------------------------
	
	function parse_entry(Entry $entry){
		$output = '';
		
		$output .= self::get_indent() . '<Entry>'."\n";
		$this->depth++;
		
		$output .= self::get_indent() . '<H>' . $entry->get_headword() . '</H>' . "\n";

		while($form = $entry->get_form()){
			$output .= $this->parse_form($form);
		}
		
		while($sense = $entry->get_sense()){
			$output .= $this->parse_sense($sense);
		}
		
		$this->depth--;
		$output .= self::get_indent() . '</Entry>'."\n";
		
		return $output;
		
	}
	
	//--------------------------------------------------------------------
	// sense parser
	//--------------------------------------------------------------------
	
	function parse_sense(Sense $sense){
		$output = '';
		
		$output .= self::get_indent() . '<Sense>' . "\n";
		$this->depth++;
		
		$output .= self::get_indent() . '<L>' . $sense->get_label() . '</L>' . "\n";
		
		while($translation = $sense->get_translation()){
			$output .= $this->parse_translation($translation);
		}

		while($phrase = $sense->get_phrase()){
			$output .= $this->parse_phrase($phrase);
		}
		
		while($sense = $sense->get_sense()){
			$output .= $this->parse_sense($sense);
		}
		
		$this->depth--;
		$output .= self::get_indent() . '</Sense>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// phrase parser
	//--------------------------------------------------------------------
	
	function parse_phrase(Phrase $phrase){
		$output = '';
		
		$output .= self::get_indent() . '<Phrase>' . "\n";
		$this->depth++;
		
		$output .= self::get_indent() . '<H>' . $phrase->get() . '</H>' . "\n";
		
		while($translation = $phrase->get_translation()){
			$output .= $this->parse_translation($translation);
		}
		
		$this->depth--;
		$output .= self::get_indent() . '</Phrase>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// form parser
	//--------------------------------------------------------------------
	
	function parse_form(Form $form){
		$output = '';
		
		$output .= self::get_indent() . '<Form>' . "\n";
		$this->depth++;
		
		$output .= self::get_indent() . '<Grammar>' . $form->get_label() . '</Grammar>' . "\n";
		
		$output .= self::get_indent() . '<H>' . $form->get_form() . '</H>' . "\n";
		
		$this->depth--;
		$output .= self::get_indent() .  '</Form>' . "\n";
		
		return $output;
	}

	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------
	
	function parse_translation(Translation $translation){
		$output = '';
		
		$output .= self::get_indent() . '<T>' . $translation->get_text() . '</T>' . "\n";
		
		return $output;
	}
	
}

?>
