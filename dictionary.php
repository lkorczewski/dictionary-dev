<?php

namespace Dictionary;

require_once __DIR__ . '/data/data.php';

class Dictionary {
	
	private $data;
	
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
	// get metadata
	//------------------------------------------------------------------------
	
	function get_metadata(){
		
		$metadata = $this->data->access('metadata')->get();
		
		return $metadata;
	}
	
	//------------------------------------------------------------------------
	// getting list of headwords
	//------------------------------------------------------------------------
	
	function get_headwords($mask = '', $limit){
		
		$headwords = $this->data->get_headwords($mask, $limit);
		
		return $headwords;
	}
	
	//------------------------------------------------------------------------
	// getting list of entry ids
	//------------------------------------------------------------------------
	// WARNING! it is possible there are two entries with the same mask
	//------------------------------------------------------------------------
	
	function get_entry_ids(){
		
		$entry_ids = $this->data->get_entry_ids();
		
		//return $entry;
		return $entry_ids === false ? [] : $entry_ids;
	}
	
	//------------------------------------------------------------------------
	// getting entries
	//------------------------------------------------------------------------
	// to consider:
	//  - headwords by mask
	//  - rename: get_entries_by_headword
	//------------------------------------------------------------------------
	
	function get_entry_by_id($entry_id){
		
		$entry = $this->data->get_entry_by_id($this, $entry_id);
		
		return $entry;
	}
	
	function get_entries_by_headword($headword){
		
		$entries = $this->data->get_entries_by_headword($this, $headword);
		
		return $entries;
	}
	
}
