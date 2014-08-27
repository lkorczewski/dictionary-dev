<?php

namespace Dictionary;

trait MySQL_Entry_Trait {
	
	//==================================================================
	// atomic operations: entries
	//==================================================================
	
	//------------------------------------------------------------------
	// creating entry storage (table)
	//------------------------------------------------------------------
	
	function create_entry_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `entries` (' .
			' `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT \'entry identifier\',' .
			' `node_id` int(10) unsigned NOT NULL COMMENT \'node identifier\',' .
			' PRIMARY KEY (`entry_id`),' .
			' UNIQUE KEY `node_id` (`node_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking entry storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_entry_storage(){
		$query =
			'ALTER TABLE `entries`' .
			' ADD CONSTRAINT `entries_ibfk_1`' .
			' FOREIGN KEY (`node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
}
