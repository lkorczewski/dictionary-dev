<?php

trait MySQL_Order_Label {

	//------------------------------------------------------------------
	// creating category label storage (table)
	//------------------------------------------------------------------
	
	function create_order_label_storage(){
		$query =
			'CREATE TABLE IF NOT EXISTS `order_label_systems` (' .
			' `order_label_system_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,' .
			' `name` varchar(8) COLLATE utf8_bin NOT NULL,' .
			' PRIMARY KEY (`order_label_system_id`)' .
			') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'CREATE TABLE IF NOT EXISTS `order_labels` (' .
			' `order_label_system_id` tinyint(3) unsigned NOT NULL COMMENT \'system identifier\',' .
			' `order` tinyint(3) unsigned NOT NULL COMMENT \'order\',' .
			' `label` varchar(8) COLLATE utf8_bin NOT NULL COMMENT \'label\',' .
			' PRIMARY KEY (`order_label_system_id`,`order`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'CREATE TABLE IF NOT EXISTS `order_label_system_assignments` (' .
			' `element` varchar(10) COLLATE utf8_bin NOT NULL,' .
			' `depth` tinyint(3) unsigned NOT NULL,' .
			' `order_label_system_id` tinyint(3) unsigned NOT NULL,' .
			' KEY `order_label_system_id` (`order_label_system_id`)' .
			') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		return true;
	}
	
	function fill_order_label_storage(){
		
		$query =
			'TRUNCATE TABLE `order_label_systems`' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'INSERT INTO `order_label_systems` (`order_label_system_id`, `name`)' .
			' VALUES' .
			'  (1, \'arabic\'),' .
			'  (2, \'ROMAN\'),' .
			'  (3, \'roman\'),' .
			'  (4, \'LATIN\'),' .
			'  (5, \'latin\'),' .
			'  (6, \'greek\')' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'TRUNCATE TABLE `order_labels`' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'INSERT INTO `order_labels` (`order_label_system_id`, `order`, `label`)' .
			' VALUES' .
			'  (1, 1, \'1\'),' .
			'  (1, 2, \'2\'),' .
			'  (1, 3, \'3\'),' .
			'  (1, 4, \'4\'),' .
			'  (1, 5, \'5\'),' .
			'  (1, 6, \'6\'),' .
			'  (1, 7, \'7\'),' .
			'  (1, 8, \'8\'),' .
			'  (1, 9, \'9\'),' .
			'  (1, 10, \'10\'),' .
			'  (2, 1, \'I\'),' .
			'  (2, 2, \'II\'),' .
			'  (2, 3, \'III\'),' .
			'  (2, 4, \'IV\'),' .
			'  (2, 5, \'V\'),' .
			'  (2, 6, \'VI\'),' .
			'  (2, 7, \'VII\'),' .
			'  (2, 8, \'VIII\'),' .
			'  (2, 9, \'IX\'),' .
			'  (2, 10, \'X\'),' .
			'  (3, 1, \'i\'),' .
			'  (3, 2, \'ii\'),' .
			'  (3, 3, \'iii\'),' .
			'  (3, 4, \'iv\'),' .
			'  (3, 5, \'v\'),' .
			'  (3, 6, \'vi\'),' .
			'  (3, 7, \'vii\'),' .
			'  (3, 8, \'viii\'),' .
			'  (3, 9, \'ix\'),' .
			'  (3, 10, \'x\'),' .
			'  (4, 1, \'A\'),' .
			'  (4, 2, \'B\'),' .
			'  (4, 3, \'C\'),' .
			'  (4, 4, \'D\'),' .
			'  (4, 5, \'E\'),' .
			'  (4, 6, \'F\'),' .
			'  (4, 7, \'G\'),' .
			'  (4, 8, \'H\'),' .
			'  (4, 9, \'I\'),' .
			'  (4, 10, \'J\'),' .
			'  (5, 1, \'a\'),' .
			'  (5, 2, \'b\'),' .
			'  (5, 3, \'c\'),' .
			'  (5, 4, \'d\'),' .
			'  (5, 5, \'e\'),' .
			'  (5, 6, \'f\'),' .
			'  (5, 7, \'g\'),' .
			'  (5, 8, \'h\'),' .
			'  (5, 9, \'i\'),' .
			'  (5, 10, \'j\'),' .
			'  (6, 1, \'α\'),' .
			'  (6, 2, \'β\'),' .
			'  (6, 3, \'γ\'),' .
			'  (6, 4, \'δ\'),' .
			'  (6, 5, \'ε\'),' .
			'  (6, 6, \'ζ\'),' .
			'  (6, 7, \'η\'),' .
			'  (6, 8, \'θ\'),' .
			'  (6, 9, \'ι\'),' .
			'  (6, 10, \'κ\')' .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//------------------------------------------------------------------
	// linking category label storage (creating table relations)
	//------------------------------------------------------------------
	
	function link_order_label_storage(){
		$query =
			'ALTER TABLE `order_labels`' .
			' ADD CONSTRAINT `order_labels_ibfk_1`' .
			'  FOREIGN KEY (`order_label_system_id`)' .
			'  REFERENCES `order_label_systems` (`order_label_system_id`)' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		$query =
			'ALTER TABLE `order_label_system_assignments`' .
			' ADD CONSTRAINT `order_label_system_assignments_ibfk_1`' .
			'  FOREIGN KEY (`order_label_system_id`)' .
			'  REFERENCES `order_label_systems` (`order_label_system_id`)' .
			';';
		$result = $this->database->execute($query);
		
		if(!$result)
			return false;
		
		return true;
	}

}

?>