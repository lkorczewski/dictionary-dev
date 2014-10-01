<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

abstract class MySQL_Multiple_Value extends MySQL_Mapper {
	
	protected $table_name;
	protected $element_name;
	
	//------------------------------------------------------------------
	// creating translation storage (table)
	//------------------------------------------------------------------
	// todo: test
	// todo: naming in comments
	
	function create_storage(){
		
		$query =
			"CREATE TABLE IF NOT EXISTS `$this->table_name` (" .
			" `{$this->element_name}_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'value identifier'," .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `order` int(11) unsigned NOT NULL COMMENT \'order of values within node\',' .
			" `{$this->element_name}` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'value text'," .
			" PRIMARY KEY (`{$this->element_name}_id`)," .
			' KEY `parent_node_id` (`parent_node_id`)' .
			" KEY `$this->element_name` (`$this->element_name`)" .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' . 
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking translation storage (creating table relations)
	//------------------------------------------------------------------
	// todo: test 
	
	function link_storage(){
		
		$query =
			"ALTER TABLE `$this->table_name`" .
			" ADD CONSTRAINT `{$this->table_name}_ibfk_1`" .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// creating value
	//------------------------------------------------------------------
	
	function add($parent_node_id, $value = ''){
		
		// inserting new value
		
		$query =
			"INSERT $this->table_name (parent_node_id, `order`, $this->element_name)" .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($value)}' AS $this->element_name" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			"    FROM $this->table_name" .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) v' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		// obtaining new id
		
		$value_id = $this->database->get_last_insert_id();
		
		if($value_id == 0){
			return false;
		}
		
		return $value_id;
	}
	
	//------------------------------------------------------------------
	// moving value up
	//------------------------------------------------------------------
	
	function move_up($id){
		
		$query =
			"UPDATE $this->table_name v1, $this->table_name v2" .
			' SET' .
			'  v1.order = v2.order,' .
			'  v2.order = v1.order' .
			" WHERE v1.{$this->element_name}_id = $id" .
			'  AND v1.parent_node_id = v2.parent_node_id' .
			'  AND v1.order = v2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving value down
	//------------------------------------------------------------------
	
	function move_down($id){
		
		$query =
			"UPDATE $this->table_name v1, $this->table_name v2" .
			' SET' .
			'  v1.order = v2.order,' .
			'  v2.order = v1.order' .
			" WHERE v1.{$this->element_name}_id = $id" .
			'  AND v1.parent_node_id = v2.parent_node_id' .
			'  AND v1.order = v2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// updating translation
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
		
		return true;
	}
	
	//------------------------------------------------------------------
	// deleting value
	//------------------------------------------------------------------
	
	function delete($id){
		
		// it should be much simpler
		// maybe combined queries
		// maybe the translation should be called by order, not id
		
		$this->database->start_transaction();
		
		// the order of operations doesn't permit
		// a unique (pronunciation_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving forms with greater order
		
		$query =
			"UPDATE $this->table_name v1, $this->table_name v2" .
			' SET v1.order = v1.order - 1' .
			" WHERE v2.{$this->element_name}_id = $id" .
			'  AND v1.parent_node_id = v2.parent_node_id' .
			'  AND v1.order > v2.order' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// deleting translation
		
		$query =
			"DELETE FROM $this->table_name" .
			" WHERE {$this->element_name}_id = $id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}

