<?php

require_once 'database/database.php';
require_once __DIR__.'/data.php';

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/entry.php';
require_once __DIR__.'/sense.php';
require_once __DIR__.'/translation.php';

class MySQL_Data implements Data {
	public $database;  // temporary public for duration of transition period
	
	function __construct($database){
		$this->database = $database;
	}
	
	//-------------------------------------------------------------------
	function pull_headwords(){
		
		$query = 'SELECT `headword` FROM `entries`;';
		$result = $this->database->query($query);
		
		$headwords = array();
		
		foreach($result as $row){
			$headwords[] = $row['headword'];
		}
		
		return $headwords;
	}
	
	//-------------------------------------------------------------------
	// pulling nodes
	//-------------------------------------------------------------------
	function pull_entry($headword){
		return NULL;
	}
	
	function pull_sense($sense_id){
		return NULL;
	}
	
	function pull_translation($translation_id){
		return NULL;
	}
	
	//-------------------------------------------------------------------
	// senses
	//-------------------------------------------------------------------
	function move_sense_up($sense_id){
		return NULL;
	}
	
	function move_sense_down($sense_id){
		return NULL;
	}
	
	//-------------------------------------------------------------------
	// translations
	//-------------------------------------------------------------------
	function create_translation($sense_id, $text = NULL){
		return NULL;
	}

	function update_translation($translation_id, $text){
		return NULL;
	}

	function move_translation_up($translation_id){
		return NULL;
	}
	
	function move_translation_down($translation_id){
		return NULL;
	}
	
	function delete_translation($translation_id){
		return NULL;
	}
	
}

?>
