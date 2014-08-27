<?php

namespace Dictionary;

trait MySQL_Category_Label_Trait {
	
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
	
}
