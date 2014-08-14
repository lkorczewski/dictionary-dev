<?php

namespace Dictionary;

//require_once 'dictionary/data';

//====================================================
// XML Importer
//====================================================
// TODO:
//  * XML error handling
//  * good data error handling
//  * warnings:
//   - (?) order label system not matched in the database
//====================================================

use XMLReader;
use DOMDocument;
use DOMElement;
use DOMXPath;

class XML_Importer {
	
	private $data;
	private $xpath;
	
	//--------------------------------------------------------------------
	// constructing
	//--------------------------------------------------------------------
	
	public function __construct(Data $data){
		$this->data = $data;
	}
	
	//--------------------------------------------------------------------
	// parsing XML file
	//--------------------------------------------------------------------
	
	public function parse($XML_file){
		$reader = new XMLReader();
		$reader->open($XML_file);
		$parsed_elements = ['Entry', 'Metadata'];
		while($reader->read()){
			if(
				$reader->nodeType == XMLReader::ELEMENT
				&& in_array($reader->name, $parsed_elements)
			){
				$document = new DOMDocument('1.0', 'UTF-8');
				$element = $reader->expand();
				$document->appendChild($element);
				$this->xpath = new DOMXPath($document);
				switch($reader->name){
					case 'Entry' :
						$status = $this->parse_entry($element);
						break;
					case 'Metadata' :
						$status = $this->parse_metadata($element);
						break;
				}
				if($status === false){
					return false;
				}
			}
		}
		return true;
	}
	
	//--------------------------------------------------------------------
	// parsing <metadata/> tag
	//--------------------------------------------------------------------
	// using temporary associative array
	// should be an object instead
	//--------------------------------------------------------------------
	
	private function parse_metadata(DOMElement $metadata){
		$metadata_buffer = [];
		
		$this->read_metadata($metadata, $metadata_buffer);
		
		$result = $this->data->set_metadata($metadata_buffer);
		
		return $result;
	}
	
	//--------------------------------------------------------------------
	// reading <metadata/> tag
	// into temporary structure
	//--------------------------------------------------------------------
	
	private function read_metadata(DOMElement $metadata, &$metadata_buffer){
		$senses = $this->xpath->query('Sense', $metadata);
		foreach($senses as $sense){
			$this->read_sense_metadata($sense, $metadata_buffer);
		}
	}
	
	//--------------------------------------------------------------------
	// reading metadata from <sense/> tag
	// into temporary structure
	//--------------------------------------------------------------------
	
	private function read_sense_metadata(DOMElement $sense, &$metadata_buffer){
		$depth =
			$sense->hasAttribute('depth')
			? intval($sense->getAttribute('depth'))
			: 1
		;		
		$order_label = $this->xpath->query('OrderLabel', $sense);
		$this->read_order_label_metadata(
			$order_label->item(0),
			$metadata_buffer,
			[
				'node_type'  => 'sense',
				'depth'      => $depth
			]
		);
	}
	
	//--------------------------------------------------------------------
	// reading metadata from <order_label/> tag
	// into temporary structure
	//--------------------------------------------------------------------
	
	private function read_order_label_metadata(DOMElement $order_label, &$metadata_buffer, $data){
		$metadata_buffer
			[$data['node_type']]
			[$data['depth']]
			['order_label_system']
			= $order_label->getAttribute('system');
	}
	
	//--------------------------------------------------------------------
	// parsing entry
	//--------------------------------------------------------------------
	
	private function parse_entry($entry){
		
		$node_id = $this->data->add_entry();
		
		// headwords
		$headwords = $this->xpath->query('H', $entry);
		foreach($headwords as $headword){
			$this->parse_headword($node_id, $headword);
		}
		
		// pronunciations
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
	// parsing sense
	//--------------------------------------------------------------------
	
	private function parse_sense($parent_node_id, $sense){
		
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
	// parsing phrase
	//--------------------------------------------------------------------
	
	private function parse_phrase($parent_node_id, $phrase){
		
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
	// parsing headword
	//--------------------------------------------------------------------
	
	private function parse_headword($parent_node_id, $headword){
		$this->data->add_headword($parent_node_id, $headword->nodeValue);
	}

	//--------------------------------------------------------------------
	// parsing pronunciation
	//--------------------------------------------------------------------
	
	private function parse_pronunciation($parent_node_id, $pronunciation){
		$this->data->add_pronunciation($parent_node_id, $pronunciation->nodeValue);
	}
	
	//--------------------------------------------------------------------
	// parsing category label
	//--------------------------------------------------------------------
	
	private function parse_category_label($parent_node_id, $category_label){
		$this->data->set_category_label($parent_node_id, $category_label->nodeValue);
	}

	//--------------------------------------------------------------------
	// parsing form
	//--------------------------------------------------------------------
	
	private function parse_form($parent_node_id, $form){
		$label     = $this->xpath->query('L', $form)->item(0);
		$headword  = $this->xpath->query('H', $form)->item(0);
		
		$this->data->add_form($parent_node_id, $label->nodeValue, $headword->nodeValue);
	}
	
	//--------------------------------------------------------------------
	
	private function parse_context($parent_node_id, $context){
		$this->data->set_context($parent_node_id, $context->nodeValue);
	}
	
	//--------------------------------------------------------------------
	
	private function parse_translation($parent_node_id, $translation){
		$this->data->add_translation($parent_node_id, $translation->nodeValue);
	}
	
}

