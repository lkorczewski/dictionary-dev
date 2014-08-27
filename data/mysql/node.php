<?php

namespace Dictionary;

trait MySQL_Node {
	
	//==================================================================
	// auxiliary operations: nodes
	//==================================================================
	
	//------------------------------------------------------------------
	// creating node storage (table)
	//------------------------------------------------------------------
	
	private function create_node_storage(){
		
		$query =
			'CREATE TABLE IF NOT EXISTS `nodes` (' .
			' `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,' .
			' PRIMARY KEY (`node_id`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// adding node
	//------------------------------------------------------------------
	
	function add_node(){
		
		// inserting new node
		
		$query = 'INSERT nodes () VALUES ();';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining node id
		
		$query = 'SELECT last_insert_id() AS node_id;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$node_id = $result['node_id'];
		
		return $node_id;
	}
	
}

