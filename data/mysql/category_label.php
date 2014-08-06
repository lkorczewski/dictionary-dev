<?php

namespace Dictionary;

trait MySQL_Category_Label {
	
	//==================================================================
	// atomic operations: category_labels
	//==================================================================
	
	//------------------------------------------------------------------
	// creating category label storage (table)
	//------------------------------------------------------------------
	
	function create_category_label_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `category_labels` (' .
			' `category_label_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'category label identifier\',' .
			' `label` varchar(64) COLLATE utf8_bin NOT NULL COMMENT \'category label\',' .
			' PRIMARY KEY (`category_label_id`),' .
			' UNIQUE KEY `label` (`label`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'CREATE TABLE IF NOT EXISTS `node_category_labels` (' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `category_label_id` int(10) unsigned NOT NULL COMMENT \'category label identifier\',' .
			' UNIQUE KEY `parent_node_id` (`parent_node_id`),' .
			' KEY `category_label_id` (`category_label_id`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		return true;
	}
	
	//------------------------------------------------------------------
	// linking category label storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_category_label_storage(){
		$query =
			'ALTER TABLE `node_category_labels`' .
			' ADD CONSTRAINT `node_category_labels_ibfk_1`' .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE,' .
			' ADD CONSTRAINT `node_category_labels_ibfk_2`' .
			'  FOREIGN KEY (`category_label_id`)' .
			'  REFERENCES `category_labels` (`category_label_id`)' .
			'  ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
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
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining new category label id

		$query =
			'SELECT *' .
			' FROM category_labels' .
			" WHERE label = '{$this->database->escape_string($label)}'" .
			';';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$category_label_id = $result['category_label_id'];
		
		// inserting node to category label relation
		
		$query =
			'REPLACE node_category_labels' .
			' SET' .
			"  category_label_id = $category_label_id," .
			"  parent_node_id = $parent_node_id" .
			';';
		
		$result = $this->database->execute($query);
		
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
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}

