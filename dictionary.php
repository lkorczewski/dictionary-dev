<?php

namespace Dictionary;

require_once 'database/database.php';
require_once __DIR__ . '/data.php';

class Dictionary {
	
	private $data;
	private $database;
	
	private $sources;
	
	//------------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------------
	
	function __construct(Data $data){
		$this->data = $data;
	}
	
	//------------------------------------------------------------------------
	// getting data interface
	//------------------------------------------------------------------------
	
	function get_data(){
		return $this->data;
	}
	
	//------------------------------------------------------------------------
	// getting list of headwords
	//------------------------------------------------------------------------
	
	function get_headwords(){
		
		$headwords = $this->data->pull_headwords();
		
		return $headwords;
	}
	
	//------------------------------------------------------------------------
	// getting entry
	//------------------------------------------------------------------------
	// WARNING! it is possible there are two entries with the same mask
	//------------------------------------------------------------------------
	
	function get_entry($headword){
		
		$entry = $this->data->pull_entry($this, $headword);
		
		return $entry;
	}
	
	//------------------------------------------------------------------------
	// getting entries
	//------------------------------------------------------------------------
	// to consider:
	//  - headwords by mask
	//------------------------------------------------------------------------

	function get_entries($headword_mask){
		
		$entries = $this->data->pull_entries($this, $headword_mask);
		
		return $entries;
	}
	
}

