<?php

require_once 'dictionary/dictionary.php';

require_once 'dictionary/entry.php';
require_once 'dictionary/sense.php';
require_once 'dictionary/phrase.php';

require_once 'dictionary/form.php';
require_once 'dictionary/translation.php';

require_once 'dictionary/layouts/layout.php';

class Table_Layout implements Layout {
	
	private $output;

	//--------------------------------------------------------------------
	// konstruktor
	//--------------------------------------------------------------------
	
	function __construct(){
	}
	
	//--------------------------------------------------------------------
	// entry parser
	//--------------------------------------------------------------------
	
	function parse_entry(Entry $entry){
		$output = array();
		
		while($headword = $sense->get_headwords()){
			$output['headwords'][] = $this->parse_headword($headword);
		}
		
		while($pronunciation = $sense->get_pronunciation()){
			$output['pronunciations'][] = $this->parse_headword($headword);
		}
		
		while($form = $entry->get_form()){
			$output['forms'][] = $this->parse_form($form);
		}
		
		while($translation = $sense->get_translation()){
			$output['translations'][] = $this->parse_translation($translation);
		}

		while($phrase = $sense->get_phrase()){
			$output['phrases'][] = $this->parse_phrase($phrase);
		}
		
		while($sense = $entry->get_sense()){
			$output['senses'][] = $this->parse_sense($sense);
		}
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// sense parser
	//--------------------------------------------------------------------
	
	function parse_sense(Sense $sense){
		$output = array();
		
		$output['label'] = $sense->get_label();
		
		while($form = $entry->get_form()){
			$output['forms'][] = $this->parse_form($form);
		}
		
		if($context = $sense->get_context()){
			$output['context'][] = $this->parse_context($context);
		}
		
		while($translation = $sense->get_translation()){
			$output['translations'][] = $this->parse_translation($translation);
		}

		while($phrase = $sense->get_phrase()){
			$output['phrases'][] = $this->parse_phrase($phrase);
		}
		
		while($sense = $sense->get_sense()){
			$output['senses'][] = $this->parse_sense($sense);
		}
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// phrase parser
	//--------------------------------------------------------------------
	
	function parse_phrase(Phrase $phrase){
		$output = array();
		
		$output['headword'] = $phrase->get();
		
		while($translation = $phrase->get_translation()){
			$output['label']['translations'] = $this->parse_translation($translation);
		}
		
		return $output;
	}

	//--------------------------------------------------------------------
	// headword parser
	//--------------------------------------------------------------------
	
	function parse_headword(Headword $headword){
		
		$output = $headword->get();
		
		return $output;
	}

	//--------------------------------------------------------------------
	// pronunciation parser
	//--------------------------------------------------------------------
	
	function parse_pronunciation(Pronunciation $headword){
		
		$output = $pronunciation->get();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// form parser
	//--------------------------------------------------------------------
	
	function parse_form(Form $form){
		$output = array();
		
		$output['label'] = $form->get_label();
		
		$output['headword'] = $form->get_form();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------
	
	function parse_context(Context $context){
		
		$output = $context->get();
		
		return $output;
	}
	

	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------
	
	function parse_translation(Translation $translation){
		
		$output = $translation->get();
		
		return $output;
	}
	
}

?>
