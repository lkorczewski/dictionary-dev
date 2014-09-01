<?php

namespace Dictionary;

require_once 'database/database.php';
require_once __DIR__ . '/data.php';

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/node.php';
require_once __DIR__ . '/headword_node.php';
require_once __DIR__ . '/entry.php';
require_once __DIR__ . '/sense.php';
require_once __DIR__ . '/translation.php';

require_once __DIR__ . '/data/mysql/metadata.php';
require_once __DIR__ . '/data/mysql/order_label.php';

require_once __DIR__ . '/data/mysql/node.php';
require_once __DIR__ . '/data/mysql/entry.php';
require_once __DIR__ . '/data/mysql/sense.php';
require_once __DIR__ . '/data/mysql/phrase.php';
require_once __DIR__ . '/data/mysql/headword.php';
require_once __DIR__ . '/data/mysql/pronunciation.php';
require_once __DIR__ . '/data/mysql/category_label.php';
require_once __DIR__ . '/data/mysql/form.php';
require_once __DIR__ . '/data/mysql/context.php';
require_once __DIR__ . '/data/mysql/translation.php';

require_once __DIR__ . '/data/mysql/storage_creator.php';

// for removal

use Database\Database;

class MySQL_Data implements Data {
	private $database;
	
	private $sense_depth = 0;
	
	private $mappers = [];
	
	//------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------
	
	function __construct(Database $database){
		
		$this->database = $database;
		
	}
	
	//------------------------------------------------------------------
	// creating storage (database)
	//------------------------------------------------------------------
	
	function create_storage(&$log){
		$creator = new MySQL_Storage_Creator($this);
		
		$result = $creator->run($log);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// mapper access
	//------------------------------------------------------------------
	// - difficult to find better solution than magic access
	
	function access($entity){
		
		if(!isset($this->mappers[$entity])){
			$class = 'Dictionary\MySQL_' . $entity;
			$this->mappers[$entity] = new $class($this->database, $this);
		}
		
		return $this->mappers[$entity];
	}
	
	//------------------------------------------------------------------
	// pulling list of headwords
	//------------------------------------------------------------------
	
	function get_headwords($mask = '', $limit = false){
		
		$mask_sql = $mask ? "  AND h.headword LIKE '$mask%'" : '';
		$limit_sql = $limit ? " LIMIT $limit" : '';
		
		$query =
			'SELECT DISTINCT h.headword' .
			' FROM' .
			'  entries e,' .
			'  headwords h' .
			' WHERE e.node_id = h.parent_node_id' .
			'  AND h.order = 1' .
			$mask_sql .
			' ORDER BY h.headword' .
			$limit_sql .
			';';
		$headwords = $this->database->fetch_column($query);
		
		return $headwords;
	}
	
	//------------------------------------------------------------------
	// getting entries from database by headword
	//------------------------------------------------------------------
	
	function get_entries_by_headword(Dictionary $dictionary, $headword){
		
		$query =
			'SELECT DISTINCT e.*' . // why distinct?
			' FROM' .
				' headwords h,' .
				' entries e' .
			' WHERE' .
				" h.headword = '{$this->database->escape_string($headword)}'" .
			' AND' .
				' e.node_id = h.parent_node_id' .
			';';
		$entries_result = $this->database->fetch_all($query);
		
		// poniższe do poprawki
		if($entries_result == false || count($entries_result) == 0){
			return false;
		}
		
		$entries = [];
		
		foreach($entries_result as $entry_result){
			$entries[] = $this->make_entry($dictionary, $entry_result);
		}
		
		return $entries;
		
	}
	
	//==================================================================
	// parser access
	//==================================================================
	// there needs to be a way to access all the entries one by one
	// in order to dump all the data; the current method is a bit
	// imperfect, because it outputs an inner id data that should be
	// of no interest to the user; possible solution:
	//  - some sort of custom iterator
	//     while($entry = $dictionary->get_one_entry())
	//  - iterator trait
	//     foreach($dictionary as $entry)
	
	//------------------------------------------------------------------
	// getting a list of entry node ids
	//------------------------------------------------------------------
	// the result is alphabetical to the extent of alphabetic order
	// of MySQL's utf8_bin
	//------------------------------------------------------------------
	
	function get_entry_ids(){
	
		$query =
			'SELECT' .
				' n.node_id' .
			' FROM' .
				' nodes n,' .
				' entries e,' .
				' headwords h' .
			' WHERE' .
				' e.node_id = n.node_id' .
			' AND' .
				' h.parent_node_id = n.node_id' .
			' AND' .
				' h.order = 1' .
			' ORDER BY' .
				' h.headword' .
			';';
		$entry_node_ids = $this->database->fetch_column($query);
		
		return $entry_node_ids;
	}
	
	//------------------------------------------------------------------
	// getting entry by node id
	//------------------------------------------------------------------
	
	function get_entry_by_id(Dictionary $dictionary, $node_id){
		$query =
			'SELECT *' .
			' FROM entries' .
			" WHERE node_id = '$node_id'" .
			';';
		$entry_result = $this->database->fetch_one($query);
		
		$entry = $this->make_entry($dictionary, $entry_result);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_entry_children(Entry $entry){
		
		// headwords
		$this->access('headword')->hydrate_node($entry);
		
		// pronunciation
		$this->access('pronunciation')->hydrate_node($entry);
		
		// headword node
		$this->pull_headword_node_children($entry);
		
		// phrases
		$this->_pull_phrases($entry);
		
		// senses
		$this->_pull_senses($entry);
		
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
		$this->_pull_phrases($sense);
		
		// senses
		$this->_pull_senses($sense);
		
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
		$this->access('category_label')->hydrate_node($node);
		
		// forms
		$this->access('form')->hydrate_node($node);
		
		// node
		$this->pull_node_children($node);
		
	}
	
	//------------------------------------------------------------------
	// pulling node children from database
	//------------------------------------------------------------------
	
	private function pull_node_children(Node $node){
		
		// translations
		$this->access('translation')->hydrate_node($node);
		
	}
	
	//==================================================================
	// pulling ...
	//==================================================================
	
	private function _pull_senses(Node_With_Senses $node){
	
		$this->sense_depth++;
		
		$order_labels_query =
			'SELECT ol.order, ol.label' .
			' FROM' .
			'  order_label_system_assignments olsa,' .
			'  order_labels ol' .
			' WHERE olsa.order_label_system_id = ol.order_label_system_id' .
			'  AND olsa.element = \'sense\'' .
			"  AND olsa.depth = {$this->sense_depth}"
			;
		$query =
			'SELECT s.*, ol.label AS order_label' .
			' FROM' .
			'  senses s' .
			'   LEFT JOIN (' . $order_labels_query . ') ol' .
			'    ON ol.order = s.order' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY s.`order`' .
			';';
		$senses_result = $this->database->fetch_all($query);
		
		foreach($senses_result as $sense_result){
			$sense = $node->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_node_id($sense_result['node_id']);
			$sense->set_label($sense_result['order_label']);
			$this->pull_sense_children($sense);
		}
		
		$this->sense_depth--;
		
	}
	
	//------------------------------------------------------------------
	
	private function _pull_phrases(Node_With_Phrases $node){
		
		$query =
			'SELECT *' .
			' FROM phrases' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$phrases_result = $this->database->fetch_all($query);
		
		foreach($phrases_result as $phrase_result){
			$phrase = $node->add_phrase();
			$phrase->set_id($phrase_result['phrase_id']);
			$phrase->set_node_id($phrase_result['node_id']);
			$phrase->set($phrase_result['phrase']);
			$this->pull_phrase_children($phrase);
		}
		
	}
	
	//==================================================================
	// making ...
	//==================================================================
	
	private function make_entry(Dictionary $dictionary, $entry_result){
		$entry = new Entry($dictionary);
		
		$entry->set_id($entry_result['entry_id']);
		$entry->set_node_id($entry_result['node_id']);
		
		$this->pull_entry_children($entry);
		
		return $entry;
	}
	
}

