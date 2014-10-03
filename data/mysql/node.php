<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/mapper.php';

class MySQL_Node extends MySQL_Mapper {
	
	//------------------------------------------------------------------
	// creating node storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
		
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
	// pulling node children
	//------------------------------------------------------------------
	
	function pull_headword_node_children(Headword_Node $node){
		
		// category label
		$this->data->access('category_label')->hydrate_node($node);
		
		// forms
		$this->data->access('form')->hydrate_node($node);
		
		// node
		$this->pull_children($node);
	}
	
	function pull_children(Node $node){
		
		// translations
		$this->data->access('translation')->hydrate_node($node);
		
	}
	
	//------------------------------------------------------------------
	// adding node
	//------------------------------------------------------------------
	
	function add(){
		
		// inserting new node
		
		$query = 'INSERT nodes () VALUES ();';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		// obtaining node id
		
		$node_id = $this->database->get_last_insert_id();
		
		if($node_id === 0){
			return false;
		}
		
		return $node_id;
	}
	
	//------------------------------------------------------------------
	// adding node
	//------------------------------------------------------------------
	
	function delete($node_id){
		$query =
			'DELETE FROM nodes' .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
}

