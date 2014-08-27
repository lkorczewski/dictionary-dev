<?php

namespace Dictionary;

trait MySQL_Phrase_Trait {
	
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
	
}
