<?php
	
trait MySQL_Entry {

	//==================================================================
	// atomic operations: entries
	//==================================================================
	
	//------------------------------------------------------------------
	// adding entry
	//------------------------------------------------------------------
	
	function add_entry($headword = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new entry
		
		$query =
			'INSERT entries' .
			' SET' .
			'  node_id = last_insert_id()' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// inserting headword
		
		if($headword){
			
			$query =
				'INSERT headwords' .
				' SET' .
				"  parent_node_id = $node_id," .
				'  `order` = 1,' .
				"  headword = '{$this->database->escape_string($headword)}'" .
				';';
			$result = $this->database->query($query);
			
			if($result === false) return false;
			
		}
				
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
	}
	
	//------------------------------------------------------------------
	// updating entry
	//------------------------------------------------------------------
	
	function update_entry($node_id, $headword){
		
		$query =
			'UPDATE entries' .
			" SET headword = '{$this->database->escape_string($headword)}'" .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// deleting entry
	//------------------------------------------------------------------
	
	function delete_entry($node_id){
		
		$query =
			'DELETE FROM entries' .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		if($result === false) return false;
		
		return true;
	}
	
}

?>