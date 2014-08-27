<?php

namespace Dictionary;

trait MySQL_Form_Trait {
	
	//==================================================================
	// atomic operations: forms
	//==================================================================
	
	//------------------------------------------------------------------
	// creating form storage (table)
	//------------------------------------------------------------------
	
	function create_form_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `forms` (' .
			' `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'form identifier\',' .
			' `parent_node_id` int(10) unsigned NOT NULL COMMENT \'parent node identifier\',' .
			' `order` int(11) NOT NULL COMMENT \'order of forms within node\',' .
			' `label` varchar(32) COLLATE utf8_bin NOT NULL COMMENT \'label\',' .
			' `form` varchar(256) COLLATE utf8_bin NOT NULL COMMENT \'form\',' .
			' PRIMARY KEY (`form_id`),' .
			' KEY `parent_node_id` (`parent_node_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT=\'grammatical forms of headword\'' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking form storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_form_storage(){
		$query =
			'ALTER TABLE `forms`' .
			' ADD CONSTRAINT `forms_ibfk_1`' .
			' FOREIGN KEY (`parent_node_id`)' .
			' REFERENCES `nodes` (`node_id`)' .
			' ON DELETE CASCADE' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
}
