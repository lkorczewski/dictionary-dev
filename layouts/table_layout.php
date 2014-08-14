<?php

require_once __DIR__ . '/../dictionary.php';

require_once __DIR__ . '/../entry.php';
require_once __DIR__ . '/../sense.php';
require_once __DIR__ . '/../phrase.php';

require_once __DIR__ . '/../form.php';
require_once __DIR__ . '/../translation.php';

require_once __DIR__ . '/../layouts/layout.php';

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
		$output = [];
		
		foreach($sense->get_headwords() as $headword){
			$output['headwords'][] = $this->parse_headword($headword);
		}
		
		foreach($sense->get_pronunciations() as $pronunciation){
			$output['pronunciations'][] = $this->parse_headword($pronunciation);
		}
		
		foreach($entry->get_forms() as $form){
			$output['forms'][] = $this->parse_form($form);
		}
		
		foreach($sense->get_translations() as $translation){
			$output['translations'][] = $this->parse_translation($translation);
		}

		foreach($sense->get_phrases() as $phrase){
			$output['phrases'][] = $this->parse_phrase($phrase);
		}
		
		foreach($entry->get_senses() as $sense){
			$output['senses'][] = $this->parse_sense($sense);
		}
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// sense parser
	//--------------------------------------------------------------------
	
	function parse_sense(Sense $sense){
		$output = [];
		
		$output['label'] = $sense->get_label();
		
		foreach($entry->get_forms() as $form){
			$output['forms'][] = $this->parse_form($form);
		}
		
		if($context = $sense->get_context()){
			$output['context'][] = $this->parse_context($context);
		}
		
		foreach($sense->get_translations() as $translation){
			$output['translations'][] = $this->parse_translation($translation);
		}

		foreach($sense->get_phrases() as $phrase){
			$output['phrases'][] = $this->parse_phrase($phrase);
		}
		
		foreach($sense->get_senses() as $sense){
			$output['senses'][] = $this->parse_sense($sense);
		}
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// phrase parser
	//--------------------------------------------------------------------
	
	function parse_phrase(Phrase $phrase){
		$output = [];
		
		$output['headword'] = $phrase->get();
		
		foreach($phrase->get_translations() as $translation){
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
		$output = [];
		
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

