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
		$output['headword'] = $entry->get_headword();
		
		while($form = $entry->get_form()){
			$output['forms'][] = $this->parse_form($form);
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
		
		while($translation = $sense->get_translation()){
			$output['translations'] = $this->parse_translation($translation);
		}

		while($phrase = $sense->get_phrase()){
			$output['phrases'] = $this->parse_phrase($phrase);
		}
		
		while($sense = $sense->get_sense()){
			$output['senses'] = $this->parse_sense($sense);
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
	
	function parse_translation(Translation $translation){
		
		$output =  $translation->get_text();
		
		return $output;
	}
	
}

?>
