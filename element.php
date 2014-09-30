<?php

namespace Dictionary;

abstract class Element {
	protected static $snakized_name;
	protected static $camelized_name;
	
	protected $dictionary;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Dictionary $dictionary){
		$this->dictionary = $dictionary;
	}
	
	//------------------------------------------------
	// names
	//------------------------------------------------
	
	static function get_snakized_name(){
		return static::$snakized_name;
	}
	
	static function get_camelized_name(){
		return static::$camelized_name;
	}
} 