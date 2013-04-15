<?php

require_once 'database/database.php';
require_once __DIR__.'/data.php';

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/entry.php';
require_once __DIR__.'/sense.php';
require_once __DIR__.'/translation.php';

class MySQL_Data implements Data {
	public $database;  // temporary public for duration of transition period

	//------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------
	
	function __construct($database){
		
		$this->database = $database;
		
	}
	
	//------------------------------------------------------------------
	// pulling list of headwords
	//------------------------------------------------------------------
	function pull_headwords(){
		
		$query = 'SELECT `headword` FROM `entries`;';
		$result = $this->database->query($query);
		
		$headwords = array();
		
		foreach($result as $row){
			$headwords[] = $row['headword'];
		}
		
		return $headwords;
		
	}
	
	//------------------------------------------------------------------
	// pulling entry from database
	//------------------------------------------------------------------
	
	function pull_entry(Entry $entry, $headword){
		// to do: only the first headword if all are the same
		
		$query = "SELECT * FROM entries WHERE headword = '$headword';"; // needs escaping!
		$entry_result = $this->database->query($query);
		
		$entry->set_id($entry_result[0]['entry_id']);
		$entry->set_headword($entry_result[0]['headword']);
		
		$this->pull_entry_children($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_entry_children(Entry $entry){
		
		$query =
			'SELECT *'.
			' FROM senses'.
			" WHERE entry_id = {$entry->get_id()} AND parent_sense_id IS NULL".
			' ORDER BY `order`'.
			';';
		$senses_result = $this->database->query($query);
		
		foreach($senses_result as $sense_result){
			$sense = $entry->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_label($sense_result['label']);
			$this->pull_sense_children($sense);
		}
		
		return $entry;
		
	}
	
	//------------------------------------------------------------------
	// filling sense children from database
	//------------------------------------------------------------------
	
	private function pull_sense_children(Sense $sense){
		
		// translations
		
		$query = "SELECT * FROM translations WHERE sense_id = {$sense->get_id()} ORDER BY `order`;";
		$translations_result = $this->database->query($query);
		
		foreach($translations_result as $translation_result){
			$translation = $sense->add_translation();
			$translation->set_id($translation_result['translation_id']);
			$translation->set_text($translation_result['text']);
		}
		
		// subsenses
		
		$query = "SELECT * FROM senses WHERE parent_sense_id = {$sense->get_id()} ORDER BY `order`;";
		$subsenses_result = $this->database->query($query);
		
		foreach($subsenses_result as $subsense_result){
			$subsense = $this->add_sense();
			$subsense->set_id($subsense_result['sense_id']);
			$subsense->set_label($subsense_result['label']);
			$subsense->pull();
		}
		
		return $sense;
		
	}
	
	//==================================================================
	// atomic operations: senses
	//==================================================================
	
	//------------------------------------------------------------------
	// moving sense up
	//------------------------------------------------------------------

	function move_sense_up($sense_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.sense_id = $sense_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;

	}

	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_sense_down($sense_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.sense_id = $sense_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;

	}
	
	//==================================================================
	// atomic operations: translations
	//==================================================================
	
	//------------------------------------------------------------------
	// creating translation
	//------------------------------------------------------------------

	function add_translation($sense_id, $text = ''){
		
		$query =
			'INSERT translations (sense_id, parent_node_id, `order`, text)' .
			" SELECT sense_id, node_id, MAX(new_order), '$text'" .
			'  FROM (' .
			'   SELECT s.sense_id, s.node_id, MAX(t.order) + 1 AS new_order' .
			'    FROM translations t, senses s' .
			"    WHERE s.`sense_id` = $sense_id" .
			'     AND s.node_id = t.parent_node_id' .
			'    GROUP BY s.sense_id, s.node_id' .
			'   UNION SELECT sense_id, node_id, 1 AS new_order' .
			'    FROM senses' .
			"    WHERE sense_id = $sense_id" .
			'  ) t' .
			';';
		
		$result = $this->database->query($query);

		if($result === false){
			return false;
		}

		$query = 'SELECT last_insert_id() AS `insert_id`;';
		$result = $this->database->query($query);

		if($result === false){
			return false;
		}
		
		$translation_id = $result[0]['insert_id'];
		
		return $translation_id;
		
	}
	
	//------------------------------------------------------------------
	// updating translation
	//------------------------------------------------------------------
	
	function update_translation($translation_id, $text){
		
		$query =
			'UPDATE translations' .
			" SET text = '$text'" .
			" WHERE translation_id = $translation_id" .
			';';
		$result = $database->query($query);
		
		if($result === false){
			return false;
		}
		
		return true;
		
	}
	
	//------------------------------------------------------------------
	// moving translation up
	//------------------------------------------------------------------

	function move_translation_up($translation_id){
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET t1.order = t2.order, t2.order = t1.order' .
			" WHERE t1.translation_id = $translation_id" .
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order = t2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_translation_down($translation_id){
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET t1.order = t2.order, t2.order = t1.order' .
			" WHERE t1.translation_id = $translation_id" .
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order = t2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// deleting translation
	//------------------------------------------------------------------
	
	function delete_translation($translation_id){
		
		// it should be much simplier
		// maybe combined queries
		// maybe the translation should be called by order, not id

		$this->database->start_transaction();

		// the order of operations doesn't permit
		// a unique (sense_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving translations with greater order
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET t1.order = t1.order - 1' .
			" WHERE t2.translation_id = $translation_id" .
			'  AND t1.sense_id = t2.sense_id' .
			'  AND t1.order > t2.order' .
			';';
		$result = $this->database->query($query);

		if($result === false){
			return false;
		}
		
		// deleting translation
		
		$query =
			'DELETE FROM `translations`' .
			" WHERE `translation_id` = $translation_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false){
			return false;
		}
		
		$this->database->commit_transaction();
		
		return true;
		
	}

}

?>
