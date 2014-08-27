<?php

namespace Dictionary;

trait MySQL_Headword_Trait {
	
	//==================================================================
	// atomic operations: headwords
	//==================================================================
	
	//------------------------------------------------------------------
	// creating headword storage (table)
	//------------------------------------------------------------------
	
	function create_headword_storage(){
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
	
	function link_headword_storage(){
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
	
}
