<?php

namespace Dictionary;

abstract class MySQL_Label extends MySQL_Mapper {
	
	protected $table_name;
	protected $element_name;
	
	//------------------------------------------------------------------
	// creating label storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
		
		$result = $this->create_label_storage();
		
		if($result === false){
			return false;
		}
		
		$result = $this->create_label_assignment_storage();
		
		if($result === false)
			return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	
	protected function create_label_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `category_labels` (' .
			' `category_label_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'label identifier\',' .
			' `label` varchar(64) COLLATE utf8_bin NOT NULL COMMENT \'label\',' .
			' PRIMARY KEY (`category_label_id`),' .
			' UNIQUE KEY `label` (`label`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	
	protected function create_label_assignment_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `node_category_labels` (' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `category_label_id` int(10) unsigned NOT NULL COMMENT \'label identifier\',' .
			' UNIQUE KEY `parent_node_id` (`parent_node_id`),' .
			' KEY `category_label_id` (`category_label_id`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking label storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_storage(){
		$query =
			"ALTER TABLE `node_{$this->table_name}`" .
			" ADD CONSTRAINT `node_{$this->table_name}_ibfk_1`" .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE,' .
			" ADD CONSTRAINT `node_{$this->table_name}_ibfk_2`" .
			"  FOREIGN KEY (`{$this->element_name}_id`)" .
			"  REFERENCES `{$this->table_name}` (`{$this->element_name}_id`)" .
			'  ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// setting label
	//------------------------------------------------------------------
	
	function set($parent_node_id, $label){
		
		$this->database->start_transaction();
		
		/*
		$result = $this->insert_label_if_not_exists($label)
			&& ($label_id = $this->select_label_id($label))
			&& $this->replace_label_assignment($parent_node_id, $label_id)
			&& $this->delete_orphaned_labels();
		
		if(!$result){
			$this->database->rollback_transaction();
			return false;
		}
		*/
		
		$result = $this->insert_label_if_not_exists($label);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$label_id = $this->select_label_id($label);
		
		if($label_id === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$result = $this->replace_label_assignment($parent_node_id, $label_id);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$result = $this->delete_orphaned_labels();
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$this->database->commit_transaction();
		
		return true;
	}
	
	//------------------------------------------------------------------
	
	protected function insert_label_if_not_exists($label){
		$query =
			"INSERT IGNORE {$this->table_name}" .
			" SET label = '{$this->database->escape_string($label)}'" .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	
	protected function select_label_id($label){
		$query =
			"SELECT {$this->element_name}_id" .
			" FROM {$this->table_name}" .
			" WHERE label = '{$this->database->escape_string($label)}'" .
			';';
		$result = $this->database->fetch_value($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	
	protected function replace_label_assignment($parent_node_id, $label_id){
		$query =
			"REPLACE node_{$this->table_name}" .
			' SET' .
			"  {$this->element_name}_id = $label_id," .
			"  parent_node_id = $parent_node_id" .
			';';
		
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// deleting label
	//------------------------------------------------------------------
	
	function delete($parent_node_id){
		
		$result = $this->delete_label_assignment($parent_node_id);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		$result = $this->delete_orphaned_labels();
		
		if($result === false){
			return false;
		}
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	
	protected function delete_label_assignment($parent_node_id){
		$query =
			"DELETE FROM node_{$this->table_name}" .
			" WHERE parent_node_id = $parent_node_id" .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	
	protected function delete_orphaned_labels(){
		$query =
			"DELETE cl" .
			" FROM {$this->table_name} cl" .
			"  LEFT JOIN node_{$this->table_name} ncl" .
			"   ON ncl.{$this->element_name}_id = cl.{$this->element_name}_id" .
			"  WHERE ncl.{$this->element_name}_id IS NULL" .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
}
