<?php

trait MySQL_Headword {

	//==================================================================
	// atomic operations: headwords
	//==================================================================
	
	//------------------------------------------------------------------
	// adding headword
	//------------------------------------------------------------------
	
	function add_headword($parent_node_id, $headword){
		
		// inserting new translation
		
		$query =
			'INSERT headwords (parent_node_id, `order`, headword)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($headword)}' AS headword" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM headwords' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) h' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// obtaining new translation id

		$query = 'SELECT last_insert_id() AS `headword_id`;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$headword_id = $result['headword_id'];
		
		return $headword_id;
	}
	
	//------------------------------------------------------------------
	// updating headword
	//------------------------------------------------------------------
	
	function update_headword($headword_id, $headword){
		$query =
			'UPDATE headwords' .
			' SET' .
			"  headword = '$headword'" .
			" WHERE headword_id = $headword_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// moving headword up
	//------------------------------------------------------------------
	
	function move_headword_up($headword_id){
		
		$query =
			'UPDATE headwords h1, headwords h2' .
			' SET' .
			'  h1.order = h2.order,' .
			'  h2.order = h1.order' .
			" WHERE h1.headword_id = $headword_id" .
			'  AND h1.parent_node_id = h2.parent_node_id' .
			'  AND h1.order = h2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving headword down
	//------------------------------------------------------------------
	
	function move_headword_down($headword_id){
		
		$query =
			'UPDATE headwords h1, headwords h2' .
			' SET' .
			'  h1.order = h2.order,' .
			'  h2.order = h1.order' .
			" WHERE h1.headword_id = $headword_id" .
			'  AND h1.parent_node_id = h2.parent_node_id' .
			'  AND h1.order = h2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting headword
	//------------------------------------------------------------------
	
	function delete_headword($headword_id){
		
		// it should be much simplier
		// maybe combined queries
		// maybe the translation should be called by order, not id
		
		$this->database->start_transaction();
		
		// the order of operations doesn't permit
		// a unique (headword_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving forms with greater order
		
		$query =
			'UPDATE headwords h1, headwords h2' .
			' SET h1.order = h1.order - 1' .
			" WHERE h2.headword_id = $headword_id" .
			'  AND h1.parent_node_id = h2.parent_node_id' .
			'  AND h1.order > h2.order' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// deleting translation
		
		$query =
			'DELETE FROM headwords' .
			" WHERE headword_id = $headword_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}

?>
