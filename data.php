<?php

namespace Dictionary;

require_once __DIR__.'/dictionary.php';

interface Data {
	
	function get_entry_ids();
	function get_headwords($mask, $limit);
	
	function get_entry_by_id(Dictionary $dictionary, $id);
	function get_entries_by_headword(Dictionary $dictionary, $headword);
	
	
	// atomic operations
	
	// entries
	function add_entry($headword = '');
	//function update_entry($node_id, $headword);
	//function delete_entry($node_id);
	
	// senses
	function add_sense($parent_node_id);
	function move_sense_up($node_id);
	function move_sense_down($node_id);
	function get_sense_label($node_id);
	function delete_sense($node_id);
	
	// phrases
	function add_phrase($parent_node_id, $phrase = '');
	function move_phrase_up($node_id);
	function move_phrase_down($node_id);
	function delete_phrase($node_id);
	
	// headwords
	function add_headword($headword_id, $headword = '');
	function update_headword($headword_id, $text);
	function move_headword_up($headword_id);
	function move_headword_down($headword_id);
	function delete_headword($headword_id);
	
	// translations
	function add_translation($translation_id, $translation = '');
	function update_translation($translation_id, $text);
	function move_translation_up($translation_id);
	function move_translation_down($translation_id);
	function delete_translation($translation_id);
	
}
