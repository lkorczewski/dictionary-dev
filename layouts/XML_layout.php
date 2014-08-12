<?php

namespace Dictionary;

require_once __DIR__ . '/../dictionary.php';

require_once __DIR__ . '/../entry.php';
require_once __DIR__ . '/../sense.php';
require_once __DIR__ . '/../phrase.php';

require_once __DIR__ . '/../headword.php';
require_once __DIR__ . '/../category_label.php';
require_once __DIR__ . '/../form.php';
require_once __DIR__ . '/../context.php';
require_once __DIR__ . '/../translation.php';

require_once __DIR__ . '/layout.php';

class XML_Layout implements Layout{
	const RETURN_RESULT = false;
	
	private $depth;
	private $indent_string;
	
	private $output;

	//--------------------------------------------------------------------
	// constructor
	//--------------------------------------------------------------------
	
	public function __construct(){
		$this->depth = 0;
		$this->indent_string = ' ';
	}
	
	//--------------------------------------------------------------------
	// depth management
	//--------------------------------------------------------------------
	
	private function set_depth($depth){
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
			
			case 'Dictionary' :      return $this->parse_dictionary($object);
			
			case 'Entry' :           return $this->parse_entry($object);
			case 'Sense' :           return $this->parse_sense($object);
			case 'Phrase' :          return $this->parse_phrase($object);
			
			case 'Headword' :        return $this->parse_headword($object);
			case 'Category_Label' :  return $this->parse_category_label($object);
			case 'Form' :            return $this->parse_form($object);
			case 'Context' :         return $this->parse_context($object);
			case 'Translation' :     return $this->parse_translation($object);
			case 'Pronunciation' :   return $this->parse_pronunciation($object);
			default :                return false;
			
		}
		
	}
	
	//--------------------------------------------------------------------
	// dictionary parser
	//--------------------------------------------------------------------
	// $stream:
	//   PHP stream identifier; if self::RETURN_RESULT, output is returned
	//   as return value
	//--------------------------------------------------------------------
	
	function parse_dictionary(Dictionary $dictionary, $stream = self::RETURN_RESULT){
		$return_content = false;
		
		if($stream === self::RETURN_RESULT){
			$return_content = true;
			ob_start();
			$stream = 'php://stdout';
		}
		
		$stream = fopen($stream, 'w');

		fwrite($stream, self::get_indent() . '<Dictionary>'."\n");
		$this->depth++;
		
		// metadata
		
		$metadata = $dictionary->get_metadata();
		
		fwrite($stream, $this->parse_metadata($metadata));
		
		// entries
		
		$entry_ids = $dictionary->get_entry_ids();
		
		foreach($entry_ids as $entry_id){
			$entry = $dictionary->get_entry_by_id($entry_id);
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
	
	//====================================================================
	// metadata
	//====================================================================
	
	//--------------------------------------------------------------------
	// parser for metadata
	//--------------------------------------------------------------------
	
	function parse_metadata(Array $metadata){
		$output = '';
		
		$output .= self::get_indent() . '<Metadata>' . "\n";
		$this->depth++;
		
		// senses
		$number_of_senses = count($metadata['sense']);
		foreach($metadata['sense'] as $depth => $sense_metadata){
			if($number_of_senses == 1){
				$output .= self::get_indent() . '<Sense>';
			} else {
				$output .= self::get_indent() . '<Sense depth="' . $depth . '">' . "\n";
			}
			$this->depth++;
			
			$output .= $this->parse_order_label_metadata($sense_metadata);
			
			$this->depth--;
			$output .= self::get_indent() . '</Sense>' . "\n";
		}
		
		$this->depth--;
		$output .= self::get_indent() . '</Metadata>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// parser for order label metadata
	//--------------------------------------------------------------------
	
	private function parse_order_label_metadata($node_metadata){
		$output = '';
		
		if(isset($node_metadata['order_label_system'])){
			$output .=
				self::get_indent() .
				'<OrderLabel system="' .
					$node_metadata['order_label_system'] .
				'"/>' .
				"\n";
		}
		return $output;
	}
	
	//====================================================================
	// entries
	//====================================================================

	//--------------------------------------------------------------------
	// entry parser
	//--------------------------------------------------------------------
	
	function parse_entry(Entry $entry){
		$output = '';
		
		$output .= self::get_indent() . '<Entry>'."\n";
		$this->depth++;
		
		while($headword = $entry->get_headword()){
			$output .= $this->parse_headword($headword);
		}

		while($pronunciation = $entry->get_pronunciation()){
			$output .= $this->parse_pronunciation($pronunciation);
		}	
		
		if($category_label = $entry->get_category_label()){
			$output .= $this->parse_category_label($category_label);
		}
		
		while($form = $entry->get_form()){
			$output .= $this->parse_form($form);
		}

		while($translation = $entry->get_translation()){
			$output .= $this->parse_translation($translation);
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
		
		if($category_label = $sense->get_category_label()){
			$output .= $this->parse_category_label($category_label);
		}
		
		while($form = $sense->get_form()){
			$output .= $this->parse_form($form);
		}
		
		if($context = $sense->get_context()){
			$output .= $this->parse_context($context);
		}
		
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
	// headword parser
	//--------------------------------------------------------------------
	
	public function parse_headword(Headword $headword){
		$output = '';
		
		$output .= self::get_indent() . '<H>' . $headword->get() . '</H>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// pronunciation parser
	//--------------------------------------------------------------------
	
	function parse_pronunciation(Pronunciation $pronunciation){
		$output = '';
		
		$output .= self::get_indent() . '<P>' . $pronunciation->get() . '</P>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// category label
	//--------------------------------------------------------------------
	
	function parse_category_label(Category_Label $category_label){
		$output = '';
		
		$output .= self::get_indent() . '<CL>' . $category_label->get() . '</CL>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// form parser
	//--------------------------------------------------------------------
	
	function parse_form(Form $form){
		$output = '';
		
		$output .= self::get_indent() . '<Form>' . "\n";
		$this->depth++;
		
		$output .= self::get_indent() . '<L>' . $form->get_label() . '</L>' . "\n";
		
		$output .= self::get_indent() . '<H>' . $form->get_form() . '</H>' . "\n";
		
		$this->depth--;
		$output .= self::get_indent() .  '</Form>' . "\n";
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// parse context
	//--------------------------------------------------------------------
	
	function parse_context(Context $context){
		$output = '';
		
		$output .= self::get_indent() . '<I>' . $context->get() . '</I>' . "\n";
		
		return $output;
	}

	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------
	
	function parse_translation(Translation $translation){
		$output = '';
		
		$output .= self::get_indent() . '<T>' . $translation->get() . '</T>' . "\n";
		
		return $output;
	}
	
}

?>
