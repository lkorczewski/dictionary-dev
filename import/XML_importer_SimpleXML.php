<?php

namespace Dictionary;

//require_once 'dictionary/data';

use XMLReader;
use DOMDocument;

//====================================================
// TODO:
//  - metadata
//====================================================

class XML_Importer {
	
	private $data;
	
	//--------------------------------------------------------------------
	
	public function __construct(Data $data){
		$this->data = $data;
	}
	
	//--------------------------------------------------------------------
	
	public function parse($XML_file){
		$reader = new XMLReader();
		$reader->open($XML_file);
		while($reader->read()){
			if($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'Entry'){
				$document = new DOMDocument('1.0', 'UTF-8');
				$entry = simplexml_import_dom($document->appendChild($reader->expand()));
				$this->parse_entry($entry);
			}
		}
	}
	
	//--------------------------------------------------------------------
	
	private function parse_entry($entry){
		
		$node_id = $this->data->add_entry();
		
		// headwords
		$headwords = $entry->H;
		foreach($headwords as $headword){
			$this->parse_headword($node_id, $headword);
		}
		
		// category label
		$category_labels = $entry->CL;
		foreach($category_labels as $category_label){
			$this->parse_category_label($node_id, $category_label);
		}
		
		// forms
		$forms = $entry->Form;
		foreach($forms as $form){
			$this->parse_form($node_id, $form);
		}
		
		// translations
		$translations = $entry->T;
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
		
		// phrases
		$phrases = $entry->Phrase;
		foreach($phrases as $phrase){
			$this->parse_phrase($node_id, $phrase);
		}
		
		// senses
		$senses = $entry->Sense;
		foreach($senses as $sense){
			$this->parse_sense($node_id, $sense);
		}
		
	}
	
	//--------------------------------------------------------------------
	
	private function parse_sense($parent_node_id, $sense){
		
		$node_id = $this->data->add_sense($parent_node_id);
		
		// context
		$context = $sense->I[0];
		if($context){
			$this->parse_context($node_id, $context);
		}
		
		// forms
		$forms = $sense->Form;
		foreach($forms as $form){
			$this->parse_form($node_id, $form);
		}
		
		// translations
		$translations = $sense->T;
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
		
		// phrases
		$phrases = $sense->Phrase;
		foreach($phrases as $phrase){
			$this->parse_phrase($node_id, $phrase);
		}
		
		// senses
		$senses = $sense->Sense;
		foreach($senses as $sense){
			$this->parse_sense($node_id, $sense);
		}
		
	}
	
	//--------------------------------------------------------------------
	
	private function parse_phrase($parent_node_id, $phrase){
		
		$headwords = $phrase->H;
		$headword = $headwords[0];
		$node_id = $this->data->add_phrase($parent_node_id, (string) $headword);
		
		// translations
		$translations = $phrase->T;
		foreach($translations as $translation){
			$this->parse_translation($node_id, $translation);
		}
	}
	
	//--------------------------------------------------------------------
	
	private function parse_headword($parent_node_id, $headword){
		$this->data->add_headword($parent_node_id, (string) $headword);
	}
	
	//--------------------------------------------------------------------
	
	private funtion parse_category_label($parent_node_id, $category_label){
		$this->data->set_category_label($parent_node_id, (string) $category_label);
	}
	
	//--------------------------------------------------------------------
	
	private function parse_form($parent_node_id, $form){
		
		$label = $form->L[0];
		
		$headword = $form->H[0]; 
		
		$this->data->add_form($parent_node_id, (string) $label, (string) $headword);
	}
	
	//--------------------------------------------------------------------
	
	private function parse_context($parent_node_id, $context){
		$this->data->set_context($parent_node_id, (string) $context);
	}
	
	//--------------------------------------------------------------------
	
	private function parse_translation($parent_node_id, $translation){
		$this->data->add_translation($parent_node_id, (string) $translation);
	}
	
}

?>
