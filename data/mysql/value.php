<?php

namespace Dictionary;

require_once __DIR__ . '/mapper.php';

abstract class MySQL_Value extends MySQL_Mapper {
	
	protected $table_name;
	protected $element_name;
	
	//------------------------------------------------------------------
	// moving translation up
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
	// moving translation down
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
	// deleting pronunciation
	//------------------------------------------------------------------
	
	function delete($id){
		
		// it should be much simplier
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

