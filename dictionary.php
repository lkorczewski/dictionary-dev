<?php

namespace Dictionary;

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/entry.php';

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
	// get metadata
	//------------------------------------------------------------------------
	
	function get_metadata(){
		
		$metadata = $this->data->get_metadata();
		
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
	
	function get_entry_ids(){
		
		$entry_ids = $this->data->get_entry_ids();
		
		//return $entry;
		return $entry_ids === false ? [] : $entry_ids;
	}
	
	//------------------------------------------------------------------------
	// getting entry
	//------------------------------------------------------------------------
	// deprecated!
	/*
	function get_entry($headword){
		
		$entry = $this->data->pull_entry($this, $headword);
		
		return $entry;
	}
	*/
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
