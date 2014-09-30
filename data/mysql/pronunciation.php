<?php

namespace Dictionary;

require_once __DIR__ . '/value.php';

class MySQL_Pronunciation extends MySQL_Value {
	
	protected $table_name    = 'pronunciations';
	protected $element_name  = 'pronunciation';
	
	//------------------------------------------------------------------
	// creating pronunciations storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
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
	
	function link_storage(){
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
	
	//------------------------------------------------------------------
	// hydrating a node with pronunciations
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Pronunciations $node){
		
		$query =
			'SELECT *' .
			' FROM pronunciations' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$pronunciations_result = $this->database->fetch_all($query);
		
		foreach($pronunciations_result as $pronunciation_result){
			$pronunciation = $node->add_pronunciation();
			$pronunciation->set_id($pronunciation_result['pronunciation_id']);
			$pronunciation->set($pronunciation_result['pronunciation']);
		}
		
	}
	
	//------------------------------------------------------------------
	// adding pronunciation
	//------------------------------------------------------------------
	
	function add($parent_node_id, $pronunciation = ''){
		
		// inserting new translation
		
		$query =
			'INSERT pronunciations (parent_node_id, `order`, pronunciation)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($pronunciation)}' AS pronunciation" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM pronunciations' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) p' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining new pronunciation id
		
		$query = 'SELECT last_insert_id() AS `pronunciation_id`;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$pronunciation_id = $result['pronunciation_id'];
		
		return $pronunciation_id;
	}
	
	//------------------------------------------------------------------
	// updating pronunciation
	//------------------------------------------------------------------
	
	function update($pronunciation_id, $pronunciation){
		$query =
			'UPDATE pronunciations' .
			' SET' .
			"  pronunciation = '$pronunciation'" .
			" WHERE pronunciation_id = $pronunciation_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
		
	}
	
}

