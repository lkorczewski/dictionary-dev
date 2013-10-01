<?php

trait MySQL_Context {
	
	//==================================================================
	// atomic operations: contexts
	//==================================================================

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
		$result = $this->database->query($query);
		
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
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}

?>
