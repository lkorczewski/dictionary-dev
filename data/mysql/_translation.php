<?php

namespace Dictionary;

trait MySQL_Translation_Trait {
	
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
	
}
