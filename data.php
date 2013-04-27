<?php

require_once __DIR__.'/entry.php';

interface Data {
	
	function pull_headwords();
	
	function pull_entry(Entry $entry, $headword);
	
	// atomic operations
	
	// entries
	function add_entry($headword = '');
	function update_entry($entry_id, $headword);
	
	// senses
	function move_sense_up($sense_id);
	function move_sense_down($sense_id);
	
	// translations
	function add_translation($sense_id, $text = '');
	function update_translation($translation_id, $text);
	function move_translation_up($translation_id);
	function move_translation_down($translation_id);
	function delete_translation($translation_id);
	
}

?>
