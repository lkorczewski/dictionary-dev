<?php

namespace Dictionary;

class MySQL_Headword {
	
	//==================================================================
	// atomic operations: headwords
	//==================================================================
	
	//------------------------------------------------------------------
	// creating headword storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `headwords` (' .
			' `headword_id` int(10) NOT NULL AUTO_INCREMENT COMMENT \'headword identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `order` int(10) unsigned NOT NULL COMMENT \'order within parent node\',' .
			' `headword` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT \'headword\',' .
			' PRIMARY KEY (`headword_id`),' .
			' KEY `parent_node_id` (`parent_node_id`),' .
			' KEY `headword` (`headword`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking headword storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_storage(){
		$query =
			'ALTER TABLE `headwords`' .
			' ADD CONSTRAINT `headwords_ibfk_1`' .
			' FOREIGN KEY (`parent_node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// adding headword
	//------------------------------------------------------------------
	
	function add($parent_node_id, $headword = ''){
		
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
		$result = $this->database->execute($query);
		
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
	
	function update($headword_id, $headword){
		$query =
			'UPDATE headwords' .
			' SET' .
			"  headword = '$headword'" .
			" WHERE headword_id = $headword_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving headword up
	//------------------------------------------------------------------
	
	function move_up($headword_id){
		
		$query =
			'UPDATE headwords h1, headwords h2' .
			' SET' .
			'  h1.order = h2.order,' .
			'  h2.order = h1.order' .
			" WHERE h1.headword_id = $headword_id" .
			'  AND h1.parent_node_id = h2.parent_node_id' .
			'  AND h1.order = h2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving headword down
	//------------------------------------------------------------------
	
	function move_down($headword_id){
		
		$query =
			'UPDATE headwords h1, headwords h2' .
			' SET' .
			'  h1.order = h2.order,' .
			'  h2.order = h1.order' .
			" WHERE h1.headword_id = $headword_id" .
			'  AND h1.parent_node_id = h2.parent_node_id' .
			'  AND h1.order = h2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting headword
	//------------------------------------------------------------------
	
	function delete($headword_id){
		
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
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// deleting translation
		
		$query =
			'DELETE FROM headwords' .
			" WHERE headword_id = $headword_id" .
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
