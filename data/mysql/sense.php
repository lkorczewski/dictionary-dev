<?php

trait MySQL_Sense {
	
	//==================================================================
	// atomic operations: senses
	//==================================================================
	
	//------------------------------------------------------------------
	// adding sense
	//------------------------------------------------------------------
	
	function add_sense($parent_node_id, $label = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new entry
		
		$query =
			'INSERT senses (node_id, parent_node_id, `order`, label)' .
			' SELECT ' .
			'  last_insert_id() AS node_id,' .
			"  $parent_node_id AS parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			" '{$this->database->escape_string($label)}' AS label" .
			' FROM (' .
			'  SELECT MAX(`order`) + 1 AS new_order' .
			'   FROM senses' .
			"   WHERE parent_node_id = $parent_node_id" .
			'   GROUP BY parent_node_id' .
			'  UNION SELECT 1 AS new_order' .
			' ) s' .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
	}
	
	//------------------------------------------------------------------
	// moving sense up
	//------------------------------------------------------------------

	function move_sense_up($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}

	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_sense_down($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order,' .
			'  s1.label = s2.label,' .
			'  s2.label = s1.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting sense
	//------------------------------------------------------------------	
	
	function delete_sense($node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// moving senses with greater order
		
		$query =
			'UPDATE senses s1, senses s2, senses s3' .
			' SET ' .
			'  s2.order = s2.order - 1,' .
			'  s2.label = s3.label' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s2.order > s1.order' .
			'  AND s3.parent_node_id = s2.parent_node_id' .
			'  AND s3.order = s2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) {
			echo $query;
			return false;
		}
		
		// deleting node
		
		$query =
			'DELETE FROM nodes' .
			" WHERE node_id = $node_id;";
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}

?>
