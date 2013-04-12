<?php

interface Data {
	
	function pull_entry($headword);
	function pull_sense($sense_id);
	function pull_translation($translation_id);
	
	// senses
	function move_sense_up($sense_id);
	function move_sense_down($sense_id);
	
	// translations
	function create_translation($sense_id, $text = NULL);
	function update_translation($translation_id, $text);
	function move_translation_up($translation_id);
	function move_translation_down($translation_id);
	function delete_translation($translation_id);
	
}

?>
