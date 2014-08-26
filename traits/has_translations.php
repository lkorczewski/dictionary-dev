<?php

namespace Dictionary;

require_once __DIR__.'/../translation.php';
require_once __DIR__.'/node_interface.php';

interface Node_With_Translations extends Node_Interface {
	function add_translation();
	function get_translation();
	function get_translations();
}

trait Has_Translations {
	
	private $translations = [];
	private $translation_iterator = 0;
	
	//------------------------------------------------------------------------
	// translation management
	//------------------------------------------------------------------------
	
	function add_translation($translation = null){
		if(!$translation instanceof Translation){
			$translation = new Translation($this->dictionary, $translation);
		}
		$this->translations[] = $translation;
		
		return $translation;
	}
	
	function get_translation(){
		if(!isset($this->translations[$this->translation_iterator])){
			$this->translation_iterator = 0;
			return false;
		}
		
		$translation = $this->translations[$this->translation_iterator];
		$this->translation_iterator++;
		
		return $translation;
	}

	function get_translations(){
		return $this->translations;
	}
	
}
