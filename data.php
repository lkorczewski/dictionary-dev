<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

interface Data {
	
	function pull_headwords();
	
	function pull_entry(Dictionary $dictionary, $headword);
	
	// atomic operations
	
	// entries
	function add_entry($headword = '');
	//function update_entry($node_id, $headword);
	//function delete_entry($node_id);
	
	// senses
	function add_sense($parent_node_id, $label = '');
	function move_sense_up($node_id);
	function move_sense_down($node_id);
	function delete_sense($node_id);
	
	// phrases
	function add_phrase($parent_node_id, $phrase = '');
	function move_phrase_up($node_id);
	function move_phrase_down($node_id);
	function delete_phrase($node_id);
	
	// translations
	function add_translation($sense_id, $text = '');
	function update_translation($translation_id, $text);
	function move_translation_up($translation_id);
	function move_translation_down($translation_id);
	function delete_translation($translation_id);
	
}

?>
