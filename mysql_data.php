<?php

require_once 'database/database.php';
require_once __DIR__.'/data.php';

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';
require_once __DIR__.'/headword_node.php';
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
		
		$query = 'SELECT `headword` FROM `entries` ORDER BY `headword`;';
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
	
	function pull_entry(Dictionary $dictionary, $headword){
		// to do: only the first headword if all are the same
		
		$query = "SELECT * FROM entries WHERE headword = '$headword';"; // needs escaping!
		$entry_result = $this->database->query($query);
		
		if($entry_result == false || count($entry_result) == 0){
			return false;
		}
		
		$entry = new Entry($dictionary);
		
		$entry->set_id($entry_result[0]['entry_id']);
		$entry->set_node_id($entry_result[0]['node_id']);
		$entry->set_headword($entry_result[0]['headword']);
		
		$this->pull_entry_children($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_entry_children(Entry $entry){
		
		// headword_node
		
		$this->pull_headword_node_children($entry);
		
		// senses
		
		$query =
			'SELECT *' .
			' FROM senses' .
			" WHERE parent_node_id = {$entry->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$senses_result = $this->database->query($query);
		
		foreach($senses_result as $sense_result){
			$sense = $entry->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_node_id($sense_result['node_id']);
			$sense->set_label($sense_result['label']);
			$this->pull_sense_children($sense);
		}
		
		return $entry;
		
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_sense_children(Sense $sense){
		
		// headword_node
		
		$this->pull_headword_node_children($sense);
		
		// phrases
		
		$query =
			'SELECT *' .
			' FROM phrases' .
			" WHERE parent_node_id = {$sense->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$phrases_result = $this->database->query($query);
		
		foreach($phrases_result as $phrase_result){
			$phrase = $sense->add_phrase();
			$phrase->set_id($phrase_result['phrase_id']);
			$phrase->set_node_id($phrase_result['node_id']);
			$phrase->set($phrase_result['phrase']);
			$this->pull_phrase_children($phrase);
		}
		
		// subsenses
		
		$query =
			'SELECT *' .
			' FROM senses' .
			" WHERE parent_node_id = {$sense->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$subsenses_result = $this->database->query($query);
		
		foreach($subsenses_result as $subsense_result){
			$subsense = $sense->add_sense();
			$subsense->set_id($subsense_result['sense_id']);
			$subsense->set_node_id($subsense_result['node_id']);
			$subsense->set_label($subsense_result['label']);
			$this->pull_sense_children($subsense);
		}
		
		return $sense;
		
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_phrase_children(Phrase $phrase){
		
		// node
		
		$this->pull_node_children($phrase);
		
	}
	
	//------------------------------------------------------------------
	// pulling headword node children from database
	//------------------------------------------------------------------
	
	private function pull_headword_node_children(Headword_Node $node){
		
		// category label
		
		$query =
			'SELECT *' .
			' FROM node_category_labels ncl, category_labels cl' .
			' WHERE ncl.category_label_id = cl.category_label_id' .
			"  AND parent_node_id = {$node->get_node_id()}" .
			';';
		$category_labels_result = $this->database->query($query);
		
		if(is_array($category_labels_result) && count($category_labels_result)){
			$category_label_result = $category_labels_result[0];
			$category_label = $node->set_category_label();
			$category_label->set_id($category_label_result['category_label_id']);
			$category_label->set($category_label_result['label']);
		}
		
		// forms
		
		$query = 
			'SELECT *' .
			' FROM forms' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$forms_result = $this->database->query($query);
		
		foreach($forms_result as $form_result){
			$form = $node->add_form();
			$form->set_id($form_result['form_id']);
			$form->set_label($form_result['label']);
			$form->set_form($form_result['form']);
		}
		
		// node
		$this->pull_node_children($node);
		
	}
	
	//------------------------------------------------------------------
	// pulling node children from database
	//------------------------------------------------------------------
	
	private function pull_node_children(Node $node){
		
		// translations
		
		$query =
			'SELECT *' .
			' FROM translations' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$translations_result = $this->database->query($query);
		
		foreach($translations_result as $translation_result){
			$translation = $node->add_translation();
			$translation->set_id($translation_result['translation_id']);
			$translation->set_text($translation_result['text']);
		}
		
	}
	
	//==================================================================
	// auxiliary functions
	//==================================================================
	
	//------------------------------------------------------------------
	// adding node
	//------------------------------------------------------------------
	
	private function add_node(){
		
		// inserting new node

		$query = 'INSERT nodes () VALUES ();';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// obtaining node id
		
		$query = 'SELECT last_insert_id() AS node_id;';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$node_id = $result[0]['node_id'];
		
		return $node_id;
		
	}
	
	//==================================================================
	// atomic operations: entries
	//==================================================================
	
	//------------------------------------------------------------------
	// adding entry
	//------------------------------------------------------------------
	
	function add_entry($headword = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new entry
		
		$query =
			'INSERT entries (node_id, headword)' .
			' SELECT' .
			'  last_insert_id() AS node_id,' .
			"  '$headword' AS headword" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
				
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
		
	}
	
	//------------------------------------------------------------------
	// updating entry
	//------------------------------------------------------------------
	
	function update_entry($node_id, $headword){
		
		$query =
			'UPDATE entries' .
			" SET headword = '$headword'" .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		if($result === false) return false;
		
		return true;
		
	}
	
	//------------------------------------------------------------------
	// deleting entry
	//------------------------------------------------------------------
	
	function delete_entry($node_id){
		
		$query =
			'DELETE FROM entries' .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		if($result === false) return false;
		
		return true;
		
	}
	
	//==================================================================
	// atomic operations: senses
	//==================================================================
	
	//------------------------------------------------------------------
	// adding sense
	//------------------------------------------------------------------
	
	function add_sense($parent_node_id, $label = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new entry
		
		$query =
			'INSERT senses (node_id, parent_node_id, `order`, label)' .
			' SELECT ' .
			'  last_insert_id() AS node_id,' .
			"  $parent_node_id AS parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			" '$label' AS label" .
			' FROM (' .
			'  SELECT MAX(`order`) + 1 AS new_order' .
			'   FROM senses' .
			"   WHERE parent_node_id = $parent_node_id" .
			'   GROUP BY parent_node_id' .
			'  UNION SELECT 1 AS new_order' .
			' ) s' .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
		
	}
	
	//------------------------------------------------------------------
	// moving sense up
	//------------------------------------------------------------------

	function move_sense_up($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;

	}

	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_sense_down($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;

	}
	
	//------------------------------------------------------------------
	// deleting sense
	//------------------------------------------------------------------	
	
	function delete_sense($node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// moving senses with greater order
		
		$query =
			'UPDATE senses s1, senses s2, senses s3' .
			' SET ' .
			'  s2.order = s2.order - 1,' .
			'  s2.label = s3.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s2.order > s1.order' .
			'  AND s3.parent_node_id = s2.parent_node_id' .
			'  AND s3.order = s2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) {
			echo $query;
			return false;
		}
		
		// deleting node
		
		$query = "DELETE FROM nodes WHERE node_id = $node_id;";
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
		
	}
	
	//==================================================================
	// atomic operations: phrases
	//==================================================================
	
	//------------------------------------------------------------------
	// adding phrase
	//------------------------------------------------------------------
	
	function add_phrase($parent_node_id, $phrase = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new phrase
		
		$query =
			'INSERT phrases (node_id, parent_node_id, `order`, phrase)' .
			' SELECT ' .
			'  last_insert_id() AS node_id,' .
			"  $parent_node_id AS parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			" '$phrase' AS phrase" .
			' FROM (' .
			'  SELECT MAX(`order`) + 1 AS new_order' .
			'   FROM phrases' .
			"   WHERE parent_node_id = $parent_node_id" .
			'   GROUP BY parent_node_id' .
			'  UNION SELECT 1 AS new_order' .
			' ) ph' .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
		
	}
	
	//------------------------------------------------------------------
	// updating phrase
	//------------------------------------------------------------------
	
	function update_phrase($node_id, $phrase){
		
		$query =
			'UPDATE phrases' .
			" SET phrase = '$phrase'" .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		return true;
		
	}
	
	//------------------------------------------------------------------
	// moving phrase up
	//------------------------------------------------------------------
	
	function move_phrase_up($node_id){
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET' .
			'  ph1.order = ph2.order,' .
			'  ph2.order = ph1.order' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph1.order = ph2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}

	//------------------------------------------------------------------
	// moving phrase down
	//------------------------------------------------------------------
	
	function move_phrase_down($node_id){
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET' .
			'  ph1.order = ph2.order,' .
			'  ph2.order = ph1.order' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph1.order = ph2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) { echo $query; return false; }
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// deleting phrase
	//------------------------------------------------------------------
	
	function delete_phrase($node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// moving senses with greater order
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET ' .
			'  ph2.order = ph2.order - 1' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph2.order > ph1.order' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// deleting node
		
		$query =
			'DELETE FROM nodes' .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
		
	}

	//==================================================================
	// atomic operations: category_labels
	//==================================================================
	// needs rewriting: ids, storing method

	//------------------------------------------------------------------
	// setting category label
	//------------------------------------------------------------------
	
	function set_category_label($parent_node_id, $label){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// inserting category label, if it doesn't exist
		
		$query =
			'INSERT IGNORE category_labels' .
			" SET label = '$label'" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// obtaining new category label id

		$query =
			'SELECT *' .
			' FROM category_labels' .
			" WHERE label = '$label'" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$category_label_id = $result[0]['category_label_id'];
		
		// inserting node to category label relation
		
		$query =
			'REPLACE node_category_labels' .
			' SET' .
			"  category_label_id = $category_label_id," .
			"  parent_node_id = $parent_node_id" .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
		
	}
	
	//------------------------------------------------------------------
	// deleting category label
	//------------------------------------------------------------------
	
	function delete_category_label($parent_node_id){
		
		$query =
			'DELETE FROM category_labels' .
			" WHERE parent_node_id = $parent_node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//==================================================================
	// atomic operations: forms
	//==================================================================
	
	//------------------------------------------------------------------
	// creating form
	//------------------------------------------------------------------

	function add_form($parent_node_id, $label = '', $form = ''){
		
		// inserting new translation
		
		$query =
			'INSERT forms (parent_node_id, `order`, label, form)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '$label' AS label," .
			"  '$form' AS form" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM forms' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) t' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) { echo $query; return false; }
		
		// obtaining new form id

		$query = 'SELECT last_insert_id() AS form_id;';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$form_id = $result[0]['form_id'];
		
		return $form_id;
		
	}
	
	//------------------------------------------------------------------
	// updating form
	//------------------------------------------------------------------
	
	function update_form($form_id, $label, $form){
		
		$query =
			'UPDATE forms' .
			' SET' .
			" label = '$label', " .
			" form = '$form'" .
			" WHERE form_id = $form_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		return true;
		
	}
	
	//------------------------------------------------------------------
	// moving form up
	//------------------------------------------------------------------

	function move_form_up($form_id){
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET f1.order = f2.order, f2.order = f1.order' .
			" WHERE f1.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order = f2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// moving form down
	//------------------------------------------------------------------
	
	function move_form_down($form_id){
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET f1.order = f2.order, f2.order = f1.order' .
			" WHERE f1.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order = f2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// deleting translation
	//------------------------------------------------------------------
	
	function delete_form($form_id){
		
		// it should be much simplier
		// maybe combined queries
		// maybe the translation should be called by order, not id

		$this->database->start_transaction();

		// the order of operations doesn't permit
		// a unique (sense_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving forms with greater order
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET f1.order = f1.order - 1' .
			" WHERE f2.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order > f2.order' .
			';';
		$result = $this->database->query($query);

		if($result === false) return false;
		
		// deleting translation
		
		$query =
			'DELETE FROM forms' .
			" WHERE form_id = $form_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$this->database->commit_transaction();
		
		return true;
		
	}
	
	//==================================================================
	// atomic operations: translations
	//==================================================================
	
	//------------------------------------------------------------------
	// creating translation
	//------------------------------------------------------------------

	function add_translation($parent_node_id, $text = ''){
		
		// inserting new translation
		
		$query =
			'INSERT translations (parent_node_id, `order`, text)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '$text' AS text" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM translations' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) t' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) { echo $query; return false; }
		
		// obtaining new translation id

		$query = 'SELECT last_insert_id() AS `translation_id`;';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$translation_id = $result[0]['translation_id'];
		
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
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
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
		
		if($result === false) return false;
		
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
		
		if($result === false) return false;
		
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
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order > t2.order' .
			';';
		$result = $this->database->query($query);

		if($result === false) return false;
		
		// deleting translation
		
		$query =
			'DELETE FROM `translations`' .
			" WHERE `translation_id` = $translation_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$this->database->commit_transaction();
		
		return true;
		
	}

}

?>
