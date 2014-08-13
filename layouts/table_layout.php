<?php

namespace Dictionary;

require_once __DIR__ . '/../dictionary.php';

require_once __DIR__ . '/../entry.php';
require_once __DIR__ . '/../sense.php';
require_once __DIR__ . '/../phrase.php';

require_once __DIR__ . '/../form.php';
require_once __DIR__ . '/../translation.php';

require_once __DIR__ . '/layout.php';

class Table_Layout implements Layout {
	
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
		
		while($headword = $entry->get_headword()){
			$output['headwords'][] = $this->parse_headword($headword);
		}
		
		while($pronunciation = $entry->get_pronunciation()){
			$output['pronunciations'][] = $this->parse_headword($headword);
		}

		if($category_label = $entry->get_category_label()){
			$output['category_label'][] = $this->parse_category_label($category_label);
		}

		while($form = $entry->get_form()){
			$output['forms'][] = $this->parse_form($form);
		}
		
		while($translation = $entry->get_translation()){
			$output['translations'][] = $this->parse_translation($translation);
		}

		while($phrase = $entry->get_phrase()){
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
		$output = [];
		
		$output['label'] = $sense->get_label();

		if($category_label = $sense->get_category_label()){
			$output['category_label'][] = $this->parse_category_label($category_label);
		}

		while($form = $sense->get_form()){
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
		$output = [];
		
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
	
	function parse_pronunciation(Pronunciation $pronunciation){
		
		$output = $pronunciation->get();
		
		return $output;
	}

	//--------------------------------------------------------------------
	// category label parser
	//--------------------------------------------------------------------

	function parse_category_label(Category_Label $category_label){

		$output = $category_label->get();

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
