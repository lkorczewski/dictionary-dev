<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

class MySQL_Entry extends MySQL_Mapper {
	
	//------------------------------------------------------------------
	// creating entry storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `entries` (' .
			' `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT \'entry identifier\',' .
			' `node_id` int(10) unsigned NOT NULL COMMENT \'node identifier\',' .
			' PRIMARY KEY (`entry_id`),' .
			' UNIQUE KEY `node_id` (`node_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking entry storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_storage(){
		$query =
			'ALTER TABLE `entries`' .
			' ADD CONSTRAINT `entries_ibfk_1`' .
			' FOREIGN KEY (`node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// getting entries from database by headword
	//------------------------------------------------------------------
	
	function find_by_headword(Dictionary $dictionary, $headword){
		
		$query =
			'SELECT DISTINCT e.*' . // why distinct?
			' FROM' .
			'  headwords h,' .
			'  entries e' .
			' WHERE' .
			"  h.headword = '{$this->database->escape_string($headword)}'" .
			' AND' .
			'  e.node_id = h.parent_node_id' .
			';';
		$entries_result = $this->database->fetch_all($query);
		
		// poniższe do poprawki
		if($entries_result == false || count($entries_result) == 0){
			return false;
		}
		
		$entries = [];
		
		foreach($entries_result as $entry_result){
			$entries[] = $this->make($dictionary, $entry_result);
		}
		
		return $entries;
		
	}
	
	//------------------------------------------------------------------
	// getting entry by id
	//------------------------------------------------------------------
	
	function find_by_id(Dictionary $dictionary, $node_id){
		$query =
			'SELECT *' .
			' FROM entries' .
			" WHERE node_id = '$node_id'" .
			';';
		$entry_result = $this->database->fetch_one($query);
		
		if($entry_result === false){
			return false;
		}
		
		$entry = $this->make($dictionary, $entry_result);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// making entry from data
	//------------------------------------------------------------------
	
	private function make(Dictionary $dictionary, $entry_result){
		$entry = new Entry($dictionary);
		
		$entry->set_id($entry_result['entry_id']);
		$entry->set_node_id($entry_result['node_id']);
		
		$this->pull_children($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_children(Entry $entry){
		
		// headwords
		$this->data->access('headword')->hydrate_node($entry);
		
		// pronunciation
		$this->data->access('pronunciation')->hydrate_node($entry);
		
		// headword node
		$this->data->access('node')->pull_headword_node_children($entry);
		
		// phrases
		$this->data->access('phrase')->hydrate_node($entry);
		
		// senses
		$this->data->access('sense')->hydrate_node($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// adding entry
	//------------------------------------------------------------------
	
	function add($headword = ''){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->data->access('node')->add();
		
		if($node_id === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// inserting new entry
		
		$query =
			'INSERT entries' .
			' SET' .
			'  node_id = last_insert_id()' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// inserting headword
		// todo: move to headword.php
		
		if($headword){
			
			$query =
				'INSERT headwords' .
				' SET' .
				"  parent_node_id = $node_id," .
				'  `order` = 1,' .
				"  headword = '{$this->database->escape_string($headword)}'" .
				';';
			$result = $this->database->execute($query);
			
			if($result === false){
				$this->database->rollback_transaction();
				return false;
			}
			
		}
		
		// committing transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
	}
	
}
