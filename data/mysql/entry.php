<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

class MySQL_Entry extends MySQL_Mapper {
	
	//==================================================================
	// atomic operations: entries
	//==================================================================
	
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
	// adding entry
	//------------------------------------------------------------------
	
	function add($headword = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->data->add_node();
		
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
