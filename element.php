<?php

namespace Dictionary;

abstract class Element {
	protected static $snakized_name;
	protected static $camelized_name;
	
	//------------------------------------------------
	// names
	//------------------------------------------------
	
	function get_snakized_name(){
		return static::$snakized_name;
	}
	
	function get_camelized_name(){
		return static::$camelized_name;
	}
} 