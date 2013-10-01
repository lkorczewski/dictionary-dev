<?php

require_once 'database/database.php';
require_once __DIR__.'/data.php';

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';
require_once __DIR__.'/headword_node.php';
require_once __DIR__.'/entry.php';
require_once __DIR__.'/sense.php';
require_once __DIR__.'/translation.php';

require_once __DIR__.'/data/mysql/entry.php';
require_once __DIR__.'/data/mysql/sense.php';
require_once __DIR__.'/data/mysql/phrase.php';
require_once __DIR__.'/data/mysql/headword.php';
require_once __DIR__.'/data/mysql/category_label.php';
require_once __DIR__.'/data/mysql/form.php';
require_once __DIR__.'/data/mysql/context.php';
require_once __DIR__.'/data/mysql/translation.php';

class MySQL_Data implements Data {
	use MySQL_Entry;
	use MySQL_Sense;
	use MySQL_Phrase;
	use MySQL_Headword;
	use MySQL_Category_Label;
	use MySQL_Form;
	use MySQL_Context;
	use MySQL_Translation;
	
	private $database;
	
	private $sense_depth = 0;

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
		
		$query =
			'SELECT h.headword' .
			' FROM entries e, headwords h' .
			' WHERE e.node_id = h.parent_node_id' .
			'  AND h.order = 1' .
			' ORDER BY `headword`' .
			';';
		$result = $this->database->fetch_all($query);
		
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
		
		$query =
			'SELECT e.*' .
			' FROM headwords h, entries e' .
			" WHERE h.headword = '{$this->database->escape_string($headword)}'" .
			'  AND e.node_id = h.parent_node_id' .
			';';
		$entry_result = $this->database->fetch_one($query);
		
		// poniższe do poprawki
		if($entry_result == false || count($entry_result) == 0){
			return false;
		}
		
		$entry = new Entry($dictionary);
		
		$entry->set_id($entry_result['entry_id']);
		$entry->set_node_id($entry_result['node_id']);
		
		$this->pull_entry_children($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entries from database by headword
	//------------------------------------------------------------------
	
	function pull_entries(Dictionary $dictionary, $headword){
		
		$query =
			'SELECT DISTINCT e.*' .
			' FROM headwords h, entries e' .
			" WHERE h.headword = '{$this->database->escape_string($headword)}'" .
			'  AND e.node_id = h.parent_node_id' .
			';';
		$entries_result = $this->database->fetch_all($query);
		
		// poniższe do poprawki
		if($entries_result == false || count($entries_result) == 0){
			return false;
		}
		
		$entries = array();
		
		foreach($entries_result as $entry_result){
			$entry = new Entry($dictionary);
			
			$entry->set_id($entry_result['entry_id']);
			$entry->set_node_id($entry_result['node_id']);
			
			$this->pull_entry_children($entry);
			
			$entries[] = $entry;
		}
		
		return $entries;
		
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_entry_children(Entry $entry){
		
		// headword
		
		$query =
			'SELECT *' .
			' FROM headwords' .
			" WHERE parent_node_id = {$entry->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$headwords_result = $this->database->fetch_all($query);
		
		foreach($headwords_result as $headword_result){
			$headword = $entry->add_headword();
			$headword->set_id($headword_result['headword_id']);
			$headword->set($headword_result['headword']);
		}
		
		// headword_node
		
		$this->pull_headword_node_children($entry);
		
		// phrases
		
		$query =
			'SELECT *' .
			' FROM phrases' .
			" WHERE parent_node_id = {$entry->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$phrases_result = $this->database->fetch_all($query);
		
		foreach($phrases_result as $phrase_result){
			$phrase = $entry->add_phrase();
			$phrase->set_id($phrase_result['phrase_id']);
			$phrase->set_node_id($phrase_result['node_id']);
			$phrase->set($phrase_result['phrase']);
			$this->pull_phrase_children($phrase);
		}
		
		// senses
		
		$this->sense_depth++;
		
		$query =
			'SELECT s.*, ol.label AS order_label' .
			' FROM' .
			'  senses s,' .
			'  order_label_system_assignments olsa,' .
			'  order_labels ol' .
			" WHERE parent_node_id = {$entry->get_node_id()}" .
			'  AND olsa.element = \'sense\'' .
			"  AND olsa.depth = {$this->sense_depth}" .
			'  AND olsa.order_label_system_id = ol.order_label_system_id' .
			'  AND ol.order = s.order' .
			' ORDER BY `order`' .
			';';
		$senses_result = $this->database->fetch_all($query);
		
		foreach($senses_result as $sense_result){
			$sense = $entry->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_node_id($sense_result['node_id']);
			$sense->set_label($sense_result['order_label']);
			$this->pull_sense_children($sense);
		}
		
		$this->sense_depth--;
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_sense_children(Sense $sense){
		
		// context
		
		$query =
			'SELECT *' .
			' FROM contexts i' .
			" WHERE i.parent_node_id = {$sense->get_node_id()}" .
			';';
		$context_result = $this->database->fetch_one($query);
		
		if(is_array($context_result) && count($context_result)){
			$context = $sense->set_context();
			$context->set_id($context_result['context_id']);
			$context->set($context_result['context']);
		}
		
		// headword_node
		
		$this->pull_headword_node_children($sense);
		
		// phrases
		
		$query =
			'SELECT *' .
			' FROM phrases' .
			" WHERE parent_node_id = {$sense->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$phrases_result = $this->database->fetch_all($query);
		
		foreach($phrases_result as $phrase_result){
			$phrase = $sense->add_phrase();
			$phrase->set_id($phrase_result['phrase_id']);
			$phrase->set_node_id($phrase_result['node_id']);
			$phrase->set($phrase_result['phrase']);
			$this->pull_phrase_children($phrase);
		}
		
		// subsenses
		
		$this->sense_depth++;
		
		$query =
			'SELECT s.*, ol.label AS order_label' .
			' FROM' .
			'  senses s,' .
			'  order_label_system_assignments olsa,' .
			'  order_labels ol' .
			" WHERE parent_node_id = {$sense->get_node_id()}" .
			'  AND olsa.element = \'sense\'' .
			"  AND olsa.depth = {$this->sense_depth}" .
			'  AND olsa.order_label_system_id = ol.order_label_system_id' .
			'  AND ol.order = s.order' .
			' ORDER BY `order`' .
			';';
		$subsenses_result = $this->database->fetch_all($query);
		
		foreach($subsenses_result as $subsense_result){
			$subsense = $sense->add_sense();
			$subsense->set_id($subsense_result['sense_id']);
			$subsense->set_node_id($subsense_result['node_id']);
			$subsense->set_label($subsense_result['order_label']);
			$this->pull_sense_children($subsense);
		}
		
		$this->sense_depth--;
		
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
		$category_label_result = $this->database->fetch_one($query);
		
		if(is_array($category_label_result) && count($category_label_result)){
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
		$forms_result = $this->database->fetch_all($query);
		
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
		$translations_result = $this->database->fetch_all($query);
		
		foreach($translations_result as $translation_result){
			$translation = $node->add_translation();
			$translation->set_id($translation_result['translation_id']);
			$translation->set_text($translation_result['text']);
		}
		
	}
	
	//==================================================================
	// pulling ...
	//==================================================================
	
	private function pull_senses(Node $node){
		/* ... */
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
		
}

?>
