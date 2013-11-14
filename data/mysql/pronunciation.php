<?php

namespace Dictionary;

trait MySQL_Pronunciation {

	//==================================================================
	// atomic operations: pronunciations
	//==================================================================

	//------------------------------------------------------------------
	// creating pronunciations storage (table)
	//------------------------------------------------------------------
	
	function create_pronunciation_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `pronunciations` (' .
			' `pronunciation_id` int(10) NOT NULL AUTO_INCREMENT COMMENT \'pronunciation identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `order` int(10) unsigned NOT NULL COMMENT \'order within parent node\',' .
			' `pronunciation` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT \'pronunciation\',' .
			' PRIMARY KEY (`pronunciation_id`),' .
			' KEY `parent_node_id` (`parent_node_id`),' .
			' KEY `pronunciation` (`pronunciation`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking pronunciation storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_pronunciation_storage(){
		$query =
			'ALTER TABLE `pronunciations`' .
			' ADD CONSTRAINT `pronunciations_ibfk_1`' .
			' FOREIGN KEY (`parent_node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// adding pronunciation
	//------------------------------------------------------------------
	
	function add_pronunciation($parent_node_id, $pronunciation = ''){
		
		// inserting new translation
		
		$query =
			'INSERT pronunciations (parent_node_id, `order`, pronunciation)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($pronunciation)}' AS pronunciation" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM pronunciations' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) p' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining new pronunciation id

		$query = 'SELECT last_insert_id() AS `pronunciation_id`;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$pronunciation_id = $result['pronunciation_id'];
		
		return $pronunciation_id;
	}
	
	//------------------------------------------------------------------
	// updating pronunciation
	//------------------------------------------------------------------
	
	function update_pronunciation($pronunciation_id, $pronunciation){
		$query =
			'UPDATE pronunciations' .
			' SET' .
			"  pronunciation = '$pronunciation'" .
			" WHERE pronunciation_id = $pronunciation_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
	//------------------------------------------------------------------
	// moving pronunciation up
	//------------------------------------------------------------------
	
	function move_pronunciation_up($pronunciation_id){
		
		$query =
			'UPDATE pronunciations p1, pronunciations p2' .
			' SET' .
			'  p1.order = p2.order,' .
			'  p2.order = p1.order' .
			" WHERE p1.pronunciation_id = $pronunciation_id" .
			'  AND p1.parent_node_id = p2.parent_node_id' .
			'  AND p1.order = p2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving pronunciation down
	//------------------------------------------------------------------
	
	function move_pronunciation_down($pronunciation_id){
		
		$query =
			'UPDATE pronunciations p1, pronunciations p2' .
			' SET' .
			'  p1.order = p2.order,' .
			'  p2.order = p1.order' .
			" WHERE p1.pronunciation_id = $pronunciation_id" .
			'  AND p1.parent_node_id = p2.parent_node_id' .
			'  AND p1.order = p2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting pronunciation
	//------------------------------------------------------------------
	
	function delete_pronunciation($pronunciation_id){
		
		// it should be much simplier
		// maybe combined queries
		// maybe the translation should be called by order, not id
		
		$this->database->start_transaction();
		
		// the order of operations doesn't permit
		// a unique (pronunciation_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving forms with greater order
		
		$query =
			'UPDATE pronunciations p1, pronunciations p2' .
			' SET p1.order = p1.order - 1' .
			" WHERE p2.pronunciation_id = $pronunciation_id" .
			'  AND p1.parent_node_id = p2.parent_node_id' .
			'  AND p1.order > p2.order' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// deleting translation
		
		$query =
			'DELETE FROM pronunciations' .
			" WHERE pronunciation_id = $pronunciation_id" .
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

?>
