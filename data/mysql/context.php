<?php

namespace Dictionary;

trait MySQL_Context {
	
	//==================================================================
	// atomic operations: contexts
	//==================================================================
	
	//------------------------------------------------------------------
	// creating context storage (table)
	//------------------------------------------------------------------
	
	function create_context_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `contexts` (' .
			' `context_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'context identifier\', ' .
			' `parent_node_id` int(10) unsigned NOT NULL,' .
			' `context` varchar(256) COLLATE utf8_bin NOT NULL,' .
			' PRIMARY KEY (`context_id`),' .
			' UNIQUE KEY `parent_node_id` (`parent_node_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking context storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_context_storage(){
		$query =
			'ALTER TABLE `contexts`' .
			' ADD CONSTRAINT `contexts_ibfk_1`' .
			' FOREIGN KEY (`parent_node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}

	//------------------------------------------------------------------
	// setting context
	//------------------------------------------------------------------
	
	function set_context($parent_node_id, $context){
		
		// inserting node to category label relation
		
		$query =
			'INSERT contexts' .
			' SET' .
			"  parent_node_id = $parent_node_id," .
			"  context = '{$this->database->escape_string($context)}'" .
			' ON DUPLICATE KEY UPDATE' .
			"  context = '{$this->database->escape_string($context)}'" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// deleting context
	//------------------------------------------------------------------
	
	function delete_context($parent_node_id){
		
		$query =
			'DELETE FROM contexts' .
			" WHERE parent_node_id = $parent_node_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}

