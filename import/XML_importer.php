<?php

namespace Dictionary;

//require_once 'dictionary/data';

//====================================================

class XML_Importer {
	
	protected $data;
	protected $xpath;
	
	//--------------------------------------------------------------------
	
	function __construct(Data $data){
		$this->data = $data;
	}
	
	//--------------------------------------------------------------------
	
	function parse($XML_file){
		$reader = new \XMLReader();
		$reader->open($XML_file);
		while($reader->read()){
			if($reader->nodeType == \XMLReader::ELEMENT && $reader->name == 'Entry'){
				$document = new \DOMDocument('1.0', 'UTF-8');
				$entry = $reader->expand();
				$document->appendChild($entry);
				$this->xpath = new \DOMXPath($document);
				$this->parse_entry($entry);
			}
		}
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_entry($entry){
		
		$node_id = $this->data->add_entry();
		
		// headwords
		$headwords = $this->xpath->query('H', $entry);
		foreach($headwords as $headword){
			$this->parse_headword($node_id, $headword);
		}
		
		// headwords
		$pronunciations = $this->xpath->query('P', $entry);
		foreach($pronunciations as $pronunciation){
			$this->parse_pronunciation($node_id, $pronunciation);
		}
		
		// category label
		$category_labels = $this->xpath->query('CL', $entry);
		foreach($category_labels as $category_label){
			$this->parse_category_label($node_id, $category_label);
		}
		
		// forms
		$forms = $this->xpath->query('Form', $entry);
		foreach($forms as $form){
			$this->parse_form($node_id, $form);
		}
		
		// translations
		$translations = $this->xpath->query('T', $entry);
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
		
		// phrases
		$phrases = $this->xpath->query('Phrase', $entry);
		foreach($phrases as $phrase){
			$this->parse_phrase($node_id, $phrase);
		}
		
		// senses
		$senses = $this->xpath->query('Sense', $entry);
		foreach($senses as $sense){
			$this->parse_sense($node_id, $sense);
		}
		
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_sense($parent_node_id, $sense){
		
		$node_id = $this->data->add_sense($parent_node_id);
		
		// category label
		$category_labels = $this->xpath->query('CL', $sense);
		foreach($category_labels as $category_label){
			$this->parse_category_label($node_id, $category_label);
		}
		
		// forms
		$forms = $this->xpath->query('Form', $sense);
		foreach($forms as $form){
			$this->parse_form($node_id, $form);
		}
		
		// context
		$context = $this->xpath->query('I', $sense)->item(0);
		if($context){
			$this->parse_context($node_id, $context);
		}
		
		// translations
		$translations = $this->xpath->query('T', $sense);
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
		
		// phrases
		$phrases = $this->xpath->query('Phrase', $sense);
		foreach($phrases as $phrase){
			$this->parse_phrase($node_id, $phrase);
		}
		
		// senses
		$subsenses = $this->xpath->query('Sense', $sense);
		foreach($subsenses as $subsense){
			$this->parse_sense($node_id, $subsense);
		}
		
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_phrase($parent_node_id, $phrase){
		
		$headwords = $this->xpath->query('H', $phrase);
		$headword = $headwords->item(0);
		$node_id = $this->data->add_phrase($parent_node_id, $headword->nodeValue);
		
		// translations
		$translations = $this->xpath->query('T', $phrase);
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_headword($parent_node_id, $headword){
		$this->data->add_headword($parent_node_id, $headword->nodeValue);
	}

	//--------------------------------------------------------------------
	
	protected function parse_pronunciation($parent_node_id, $pronunciation){
		$this->data->add_pronunciation($parent_node_id, $pronunciation->nodeValue);
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_category_label($parent_node_id, $category_label){
		$this->data->set_category_label($parent_node_id, $category_label->nodeValue);
	}

	//--------------------------------------------------------------------
	
	protected function parse_form($parent_node_id, $form){
		$label = $this->xpath->query('L', $form)->item(0);
		$headword = $this->xpath->query('H', $form)->item(0);
		
		$this->data->add_form($parent_node_id, $label->nodeValue, $headword->nodeValue);
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_context($parent_node_id, $context){
		$this->data->set_context($parent_node_id, $context->nodeValue);
	}
	
	//--------------------------------------------------------------------
	
	protected function parse_translation($parent_node_id, $translation){
		$this->data->add_translation($parent_node_id, $translation->nodeValue);
	}
	
}

