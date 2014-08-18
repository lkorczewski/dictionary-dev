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

// TODO: output buffering instead of big string in memory

class XML_Layout implements Layout {
	const RETURN_RESULT = false;

	private $depth          = 0;
	private $indent_string  = ' ';

	private $output;

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

		$class_name        = get_class($object);
		$short_class_name  = $this->remove_prefix('Dictionary\\', $class_name);

		switch($short_class_name){

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

	protected function remove_prefix($prefix, $string){

		if (substr($string, 0, strlen($prefix)) == $prefix) {
			return substr($string, strlen($prefix));
		}

		return false;
	}

	//--------------------------------------------------------------------
	// dictionary parser
	//--------------------------------------------------------------------
	// $stream:
	//   PHP stream identifier; if false, output is returned as return
	//     value
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

		$output .= $this->parse_headwords($entry);
		$output .= $this->parse_pronunciations($entry);
		$output .= $this->parse_category_labels($entry);
		$output .= $this->parse_forms($entry);
		$output .= $this->parse_translations($entry);
		$output .= $this->parse_phrases($entry);
		$output .= $this->parse_senses($entry);

		$this->depth--;
		$output .= self::get_indent() . '</Entry>'."\n";

		return $output;

	}

	//--------------------------------------------------------------------
	// sense parser
	//--------------------------------------------------------------------

	function parse_senses(Node_With_Senses $node){
		$output = '';

		foreach($node->get_senses() as $sense){
			$output .= $this->parse_sense($sense);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_sense(Sense $sense){
		$output = '';

		$output .= self::get_indent() . '<Sense>' . "\n";
		$this->depth++;

		// TODO: is label optional?
		if($label = $sense->get_label()){
			$output .= self::get_indent() . '<L>' . $label . '</L>' . "\n";
		}

		$output .= $this->parse_category_labels($sense);
		$output .= $this->parse_forms($sense);
		$output .= $this->parse_contexts($sense);
		$output .= $this->parse_translations($sense);
		$output .= $this->parse_phrases($sense);
		$output .= $this->parse_senses($sense);

		$this->depth--;
		$output .= self::get_indent() . '</Sense>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// phrase parser
	//--------------------------------------------------------------------

	function parse_phrases(Node_With_Phrases $node){
		$output = '';

		foreach($node->get_phrases() as $phrase){
			$output .= $this->parse_phrase($phrase);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_phrase(Phrase $phrase){
		$output = '';

		$output .= self::get_indent() . '<Phrase>' . "\n";
		$this->depth++;

		$output .= self::get_indent() . '<H>' . $phrase->get() . '</H>' . "\n";

		$output .= $this->parse_translations($phrase);

		$this->depth--;
		$output .= self::get_indent() . '</Phrase>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// headword parser
	//--------------------------------------------------------------------

	function parse_headwords(Node_With_Headwords $node){
		$output = '';

		foreach($node->get_headwords() as $headword){
			$output .= $this->parse_headword($headword);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_headword(Headword $headword){
		$output = '';

		$output .= self::get_indent() . '<H>' . $headword->get() . '</H>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// pronunciation parser
	//--------------------------------------------------------------------

	function parse_pronunciations(Node_With_Pronunciations $node){
		$output = '';

		foreach($node->get_pronunciations() as $pronunciation){
			$output .= $this->parse_pronunciation($pronunciation);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_pronunciation(Pronunciation $pronunciation){
		$output = '';

		$output .= self::get_indent() . '<P>' . $pronunciation->get() . '</P>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// category label
	//--------------------------------------------------------------------
	// TODO:
	//  * should have some better naming than plural

	function parse_category_labels(Node_With_Category_Label $node){
		$output = '';

		if($category_label = $node->get_category_label()){
			$output .= $this->parse_category_label($category_label);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_category_label(Category_Label $category_label){
		$output = '';

		$output .= self::get_indent() . '<CL>' . $category_label->get() . '</CL>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// form parser
	//--------------------------------------------------------------------

	function parse_forms(Node_With_Forms $node){
		$output = '';

		foreach($node->get_forms() as $form){
			$output .= $this->parse_form($form);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_form(Form $form){
		$output = '';

		$output .= self::get_indent() . '<Form>' . "\n";
		$this->depth++;

		$output .= self::get_indent() . '<L>' . $form->get_label() . '</L>' . "\n";

		$output .= self::get_indent() . '<H>' . $form->get_form() . '</H>' . "\n";

		$this->depth--;
		$output .= self::get_indent() . '</Form>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// parse context
	//--------------------------------------------------------------------
	// TODO:
	//  * should have some better naming than plural

	function parse_contexts(Node_With_Context $node){
		$output = '';

		if($context = $node->get_context()){
			$output .= $this->parse_context($context);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_context(Context $context){
		$output = '';

		$output .= self::get_indent() . '<I>' . $context->get() . '</I>' . "\n";

		return $output;
	}

	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------

	function parse_translations(Node_With_Translations $node){
		$output = '';

		foreach($node->get_translations() as $translation){
			$output .= $this->parse_translation($translation);
		}

		return $output;
	}

	//--------------------------------------------------------------------

	function parse_translation(Translation $translation){
		$output = '';

		$output .= self::get_indent() . '<T>' . $translation->get() . '</T>' . "\n";

		return $output;
	}

}
