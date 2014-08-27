<?php

namespace Dictionary;

trait MySQL_Pronunciation_Trait {

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
	
}
