<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

class MySQL_Context extends MySQL_Mapper {
	
	//------------------------------------------------------------------
	// creating context storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
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
	
	function link_storage(){
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
	// hydrate a node with context 
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Context $node){
		
		$query =
			'SELECT *' .
			' FROM contexts i' .
			" WHERE i.parent_node_id = {$node->get_node_id()}" .
			';';
		$context_result = $this->database->fetch_one($query);
		
		if(is_array($context_result) && count($context_result)){
			$context = $node->set_context();
			$context->set_id($context_result['context_id']);
			$context->set($context_result['context']);
		}
		
	}
	
	//------------------------------------------------------------------
	// setting context
	//------------------------------------------------------------------
	
	function set($parent_node_id, $context){
		
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
	
	function delete($parent_node_id){
		
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
