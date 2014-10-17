<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

abstract class MySQL_Single_Value extends MySQL_Mapper {
	
	protected $table_name;
	protected $element_name;
	
	//------------------------------------------------------------------
	// creating context storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
		$query =
			"CREATE TABLE IF NOT EXISTS `$this->table_name` (" .
			" `{$this->element_name}_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'value identifier', " .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			" `$this->element_name` varchar(256) COLLATE utf8_bin NOT NULL COMMENT 'value text'," .
			" PRIMARY KEY (`{$this->element_name}_id`)," .
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
			"ALTER TABLE `$this->table_name`" .
			" ADD CONSTRAINT `{$this->table_name}_ibfk_1`" .
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
	
	function set($parent_node_id, $value = ''){
		
		$query =
			"INSERT $this->table_name" .
			' SET' .
			"  parent_node_id = $parent_node_id," .
			"  $this->element_name = '{$this->database->escape_string($value)}'" .
			' ON DUPLICATE KEY UPDATE' .
			"  $this->element_name = '{$this->database->escape_string($value)}'" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$context_id = $this->database->get_last_insert_id();
		
		return $context_id;
		
	}
	
	//------------------------------------------------------------------
	// updating context
	//------------------------------------------------------------------
	
	function update($id, $value){
		$query =
			"UPDATE $this->table_name" .
			" SET $this->element_name = '{$this->database->escape_string($value)}'" .
			" WHERE {$this->element_name}_id = $id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting context
	//------------------------------------------------------------------
	
	function delete($id){
		
		$query =
			"DELETE FROM $this->table_name" .
			" WHERE {$this->element_name}_id = $id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}
