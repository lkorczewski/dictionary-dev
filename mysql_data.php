<?php

namespace Dictionary;

require_once 'database/database.php';
require_once __DIR__ . '/data.php';

require_once __DIR__ . '/dictionary.php';
require_once __DIR__ . '/entry.php';

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

use Database\Database;

class MySQL_Data implements Data {
	
	private $database;
	
	public $sense_depth = 0;
	
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
	// getting entry by headword
	//------------------------------------------------------------------
	
	function get_entries_by_headword(Dictionary $dictionary, $headword){
		$headwords = $this->access('entry')->find_by_headword($dictionary, $headword);
		
		return $headwords;
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
		$entry = $this->access('entry')->find_by_id($dictionary, $node_id);
		
		return $entry;
	}
	
}
