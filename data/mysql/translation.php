<?php

namespace Dictionary;

trait MySQL_Translation {
	
	//==================================================================
	// atomic operations: translations
	//==================================================================

	//------------------------------------------------------------------
	// creating translation storage (table)
	//------------------------------------------------------------------
	
	function create_translation_storage(){
		
		$query =
			'CREATE TABLE IF NOT EXISTS `translations` (' .
			' `translation_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'translation identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `order` int(11) NOT NULL COMMENT \'order of translations within node\',' .
			' `text` varchar(64) COLLATE utf8_bin NOT NULL COMMENT \'translation text\',' .
			' PRIMARY KEY (`translation_id`),' .
			' KEY `parent_node_id` (`parent_node_id`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' . 
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking translation storage (creating table relations)
	//------------------------------------------------------------------
		
	function link_translation_storage(){
		
		$query =
			'ALTER TABLE `translations`' .
			' ADD CONSTRAINT `translations_ibfk_1`' .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// creating translation
	//------------------------------------------------------------------

	function add_translation($parent_node_id, $translation = ''){
		
		// inserting new translation
		
		$query =
			'INSERT translations (parent_node_id, `order`, text)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($translation)}' AS text" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM translations' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) t' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining new translation id

		$query = 'SELECT last_insert_id() AS `translation_id`;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$translation_id = $result['translation_id'];
		
		return $translation_id;
	}
	
	//------------------------------------------------------------------
	// updating translation
	//------------------------------------------------------------------
	
	function update_translation($translation_id, $text){
		
		$query =
			'UPDATE translations' .
			" SET text = '{$this->database->escape_string($text)}'" .
			" WHERE translation_id = $translation_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// moving translation up
	//------------------------------------------------------------------

	function move_translation_up($translation_id){
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET' .
			'  t1.order = t2.order,' .
			'  t2.order = t1.order' .
			" WHERE t1.translation_id = $translation_id" .
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order = t2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_translation_down($translation_id){
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET' .
			'  t1.order = t2.order,' .
			'  t2.order = t1.order' .
			" WHERE t1.translation_id = $translation_id" .
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order = t2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting translation
	//------------------------------------------------------------------
	
	function delete_translation($translation_id){
		
		// it should be much simplier
		// maybe combined queries
		// maybe the translation should be called by order, not id

		$this->database->start_transaction();

		// the order of operations doesn't permit
		// a unique (sense_id, order) key, that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving translations with greater order
		
		$query =
			'UPDATE translations t1, translations t2' .
			' SET t1.order = t1.order - 1' .
			" WHERE t2.translation_id = $translation_id" .
			'  AND t1.parent_node_id = t2.parent_node_id' .
			'  AND t1.order > t2.order' .
			';';
		$result = $this->database->execute($query);

		if($result === false) return false;
		
		// deleting translation
		
		$query =
			'DELETE FROM `translations`' .
			" WHERE `translation_id` = $translation_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}

