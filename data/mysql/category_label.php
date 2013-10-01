<?php

trait MySQL_Category_Label {
	
	//==================================================================
	// atomic operations: category_labels
	//==================================================================
	
	//------------------------------------------------------------------
	// setting category label
	//------------------------------------------------------------------
	
	function set_category_label($parent_node_id, $label){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// inserting category label, if it doesn't exist
		
		$query =
			'INSERT IGNORE category_labels' .
			" SET label = '{$this->database->escape_string($label)}'" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// obtaining new category label id

		$query =
			'SELECT *' .
			' FROM category_labels' .
			" WHERE label = '{$this->database->escape_string($label)}'" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$category_label_id = $result[0]['category_label_id'];
		
		// inserting node to category label relation
		
		$query =
			'REPLACE node_category_labels' .
			' SET' .
			"  category_label_id = $category_label_id," .
			"  parent_node_id = $parent_node_id" .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
	}
	
	//------------------------------------------------------------------
	// deleting category label
	//------------------------------------------------------------------
	
	function delete_category_label($parent_node_id){
		
		$query =
			'DELETE FROM node_category_labels' .
			" WHERE parent_node_id = $parent_node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}

?>
