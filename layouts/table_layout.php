<?php

namespace Dictionary;

require_once __DIR__ . '/../dictionary.php';

require_once __DIR__ . '/../entry.php';
require_once __DIR__ . '/../sense.php';
require_once __DIR__ . '/../phrase.php';

require_once __DIR__ . '/../form.php';
require_once __DIR__ . '/../translation.php';

require_once __DIR__ . '/../layouts/layout.php';

class Table_Layout implements Layout {
	// TODO: metadata
	
	//--------------------------------------------------------------------
	// entry parser
	//--------------------------------------------------------------------
	
	function parse_entry(Entry $entry){
		$output = [];
		
		$this->parse_headwords($output, $entry);
		$this->parse_pronunciations($output, $entry);
		$this->parse_forms($output, $entry);
		$this->parse_category_labels($output, $entry);
		$this->parse_translations($output, $entry);
		$this->parse_phrases($output, $entry);
		$this->parse_senses($output, $entry);
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// sense parser
	//--------------------------------------------------------------------
	
	protected function parse_senses(array &$output, Node_With_Senses $node){
		foreach($node->get_senses() as $sense){
			$output['senses'][] = $this->parse_sense($sense);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_sense(Sense $sense){
		$output = [];
		
		$output['label'] = $sense->get_label();
		
		$this->parse_category_labels($output, $sense);
		$this->parse_forms($output, $sense);
		$this->parse_contexts($output, $sense);
		$this->parse_translations($output, $sense);
		$this->parse_phrases($output, $sense);
		$this->parse_senses($output, $sense);
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// phrase parser
	//--------------------------------------------------------------------
	
	protected function parse_phrases(&$output, Node_With_Phrases $node){
		foreach($node->get_phrases() as $phrase){
			$output['phrases'][] = $this->parse_phrase($phrase);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_phrase(Phrase $phrase){
		$output = [];
		
		$output['headword'] = $phrase->get();
		
		$this->parse_translations($output, $phrase);
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// headword parser
	//--------------------------------------------------------------------
	
	protected function parse_headwords(array &$output, Node_With_Headwords $node){
		foreach($node->get_headwords() as $headword){
			$output['headwords'][] = $this->parse_headword($headword);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_headword(Headword $headword){
		
		$output = $headword->get();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// pronunciation parser
	//--------------------------------------------------------------------
	
	protected function parse_pronunciations(array &$output, Node_With_Pronunciations $node){
		foreach($node->get_pronunciations() as $pronunciation){
			$output['pronunciations'][] = $this->parse_pronunciation($pronunciation);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_pronunciation(Pronunciation $pronunciation){
		
		$output = $pronunciation->get();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// category label parser
	//--------------------------------------------------------------------
	
	protected function parse_category_labels(array &$output, Node_With_Category_Label $node){
		if($category_label = $node->get_category_label()){
			$output['category_label'] = $this->parse_category_label($category_label);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_category_label(Category_Label $category_label){
		
		$output = $category_label->get();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// form parser
	//--------------------------------------------------------------------
	
	protected function parse_forms(array &$output, Node_With_Forms $node){
		foreach($node->get_forms() as $form){
			$output['forms'][] = $this->parse_form($form);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_form(Form $form){
		$output = [];
		
		$output['label'] = $form->get_label();
		
		$output['headword'] = $form->get_form();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// context parser
	//--------------------------------------------------------------------
	
	protected function parse_contexts(array &$output, Node_With_Context $node){
		if($context = $node->get_context()){
			$output['context'] = $this->parse_context($context);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_context(Context $context){
		
		$output = $context->get();
		
		return $output;
	}
	
	//--------------------------------------------------------------------
	// translation parser
	//--------------------------------------------------------------------
	
	protected function parse_translations(array &$output, Node_With_Translations $node){
		foreach($node->get_translations() as $translation){
			$output['translations'][] = $this->parse_translation($translation);
		}
	}
	
	//--------------------------------------------------------------------
	
	function parse_translation(Translation $translation){
		
		$output = $translation->get();
		
		return $output;
	}
	
}
