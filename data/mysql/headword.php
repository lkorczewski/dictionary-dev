<?php

namespace Dictionary;

require_once __DIR__ . '/value.php';

class MySQL_Headword extends MySQL_Value {
	
	protected $table_name    = 'headwords';
	protected $element_name  = 'headword';
	
	//------------------------------------------------------------------
	// creating headword storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
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
	
	function link_storage(){
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
	
	//------------------------------------------------------------------
	// hydrating a node with headwords
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Headwords $node){
		
		$query =
			'SELECT *' .
			' FROM headwords' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$headwords_result = $this->database->fetch_all($query);
		
		foreach($headwords_result as $headword_result){
			$headword = $node->add_headword();
			$headword->set_id($headword_result['headword_id']);
			$headword->set($headword_result['headword']);
		}
		
	}
	
	//------------------------------------------------------------------
	// adding headword
	//------------------------------------------------------------------
	
	function add($parent_node_id, $headword = ''){
		
		// inserting new translation
		
		$query =
			'INSERT headwords (parent_node_id, `order`, headword)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '{$this->database->escape_string($headword)}' AS headword" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM headwords' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) h' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		// obtaining new translation id
		
		$query = 'SELECT last_insert_id() AS `headword_id`;';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$headword_id = $result['headword_id'];
		
		return $headword_id;
	}
	
	//------------------------------------------------------------------
	// updating headword
	//------------------------------------------------------------------
	
	function update($headword_id, $headword){
		$query =
			'UPDATE headwords' .
			' SET' .
			"  headword = '$headword'" .
			" WHERE headword_id = $headword_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
}

