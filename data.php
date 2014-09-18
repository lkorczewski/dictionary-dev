<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

interface Data {
	
	function get_entry_ids();
	function get_headwords($mask, $limit);
	
	function get_entry_by_id(Dictionary $dictionary, $id);
	function get_entries_by_headword(Dictionary $dictionary, $headword);
	
	function access($entity);
	
}
