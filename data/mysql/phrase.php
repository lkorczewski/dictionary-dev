<?php

namespace Dictionary;

trait MySQL_Phrase {
	
	//==================================================================
	// atomic operations: phrases
	//==================================================================
	
	//------------------------------------------------------------------
	// creating translation storage (table)
	//------------------------------------------------------------------
	
	function create_phrase_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `phrases` (' .
			' `phrase_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'phrase identifier\',' .
			' `node_id` int(10) unsigned NOT NULL COMMENT \'node identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL,' .
			' `order` tinyint(3) unsigned NOT NULL COMMENT \'order\',' .
			' `phrase` varchar(256) COLLATE utf8_bin NOT NULL COMMENT \'phrase\',' .
			' PRIMARY KEY (`phrase_id`),' .
			' UNIQUE KEY `node_id` (`node_id`),' .
			' KEY `parent_node_id` (`parent_node_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking translation storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_phrase_storage(){
		$query =
			'ALTER TABLE `phrases`' .
			' ADD CONSTRAINT `phrases_ibfk_1`' .
			'  FOREIGN KEY (`node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE' .
			'  ,' .
			' ADD CONSTRAINT `phrases_ibfk_2`' .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE' .
			';';
		
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// adding phrase
	//------------------------------------------------------------------
	
	function add_phrase($parent_node_id, $phrase = ''){
		
		// strarting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->add_node();
		
		if($node_id === false) return false;
		
		// inserting new phrase
		
		$query =
			'INSERT phrases (node_id, parent_node_id, `order`, phrase)' .
			' SELECT ' .
			'  last_insert_id() AS node_id,' .
			"  $parent_node_id AS parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			" '{$this->database->escape_string($phrase)}' AS phrase" .
			' FROM (' .
			'  SELECT MAX(`order`) + 1 AS new_order' .
			'   FROM phrases' .
			"   WHERE parent_node_id = $parent_node_id" .
			'   GROUP BY parent_node_id' .
			'  UNION SELECT 1 AS new_order' .
			' ) ph' .
			';';
		
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
	}
	
	//------------------------------------------------------------------
	// updating phrase
	//------------------------------------------------------------------
	
	function update_phrase($node_id, $phrase){
		
		$query =
			'UPDATE phrases' .
			" SET phrase = '{$this->database->escape_string($phrase)}'" .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// moving phrase up
	//------------------------------------------------------------------
	
	function move_phrase_up($node_id){
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET' .
			'  ph1.order = ph2.order,' .
			'  ph2.order = ph1.order' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph1.order = ph2.order + 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}

	//------------------------------------------------------------------
	// moving phrase down
	//------------------------------------------------------------------
	
	function move_phrase_down($node_id){
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET' .
			'  ph1.order = ph2.order,' .
			'  ph2.order = ph1.order' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph1.order = ph2.order - 1' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) { echo $query; return false; }
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting phrase
	//------------------------------------------------------------------
	
	function delete_phrase($node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// moving senses with greater order
		
		$query =
			'UPDATE phrases ph1, phrases ph2' .
			' SET ' .
			'  ph2.order = ph2.order - 1' .
			" WHERE ph1.node_id = $node_id" .
			'  AND ph1.parent_node_id = ph2.parent_node_id' .
			'  AND ph2.order > ph1.order' .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// deleting node
		
		$query =
			'DELETE FROM nodes' .
			" WHERE node_id = $node_id" .
			';';
		$result = $this->database->query($query);
		
		if($result === false) return false;
		
		// commiting transaction
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}

?>
