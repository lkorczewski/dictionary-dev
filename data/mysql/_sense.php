<?php

namespace Dictionary;

trait MySQL_Sense_Trait {
	
	//==================================================================
	// atomic operations: senses
	//==================================================================
	
	//------------------------------------------------------------------
	// creating translation storage (table)
	//------------------------------------------------------------------
	
	function create_sense_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `senses` (' .
			' `sense_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'sense identifier\',' .
			' `node_id` int(10) unsigned NOT NULL COMMENT \'node identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'node identifier of parent node\',' .
			' `order` tinyint(11) unsigned NOT NULL COMMENT \'order of senses within node\',' .
			' PRIMARY KEY (`sense_id`),' .
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
	
	function link_sense_storage(){
		$query =
			'ALTER TABLE `senses`' .
			' ADD CONSTRAINT `senses_ibfk_1`' .
			'  FOREIGN KEY (`node_id`) ' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE,' .
			' ADD CONSTRAINT `senses_ibfk_2`' .
			'  FOREIGN KEY (`parent_node_id`)' .
			'  REFERENCES `nodes` (`node_id`)' .
			'  ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
}
