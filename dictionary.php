<?php

//require_once 'debugger/debugger.php';
require_once 'database/database.php';
require_once __DIR__.'/data.php';
require_once __DIR__.'/entry.php';

class Dictionary {
	private $data;
	private $database;
	
	//------------------------------------------------
	// constructor
	//------------------------------------------------
	
	function __construct(Data $data){
		$this->data = $data;
		$this->database = $data->database;
	}
	
	//------------------------------------------------
	// getting database set
	//------------------------------------------------
	
	function get_database(){
		return $this->database;
	}

	//------------------------------------------------
	// getting data interface
	//------------------------------------------------
	
	function get_data(){
		return $this->data;
	}
	
	//------------------------------------------------
	// getting list of headwords
	//------------------------------------------------
	
	function get_headwords(){
		
		$headwords = $this->data->pull_headwords();
		
		return $headwords;
	}
	
	//------------------------------------------------
	// getting entry
	//------------------------------------------------
	
	function get_entry($headword){
		
		$entry = new Entry($this);
		$entry = $this->data->pull_entry($entry, $headword);
		
		return $entry;
	}
}

?>
