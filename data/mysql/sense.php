<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/mapper.php';

class MySQL_Sense extends MySQL_Mapper{
	
	//==================================================================
	// atomic operations: senses
	//==================================================================
	
	//------------------------------------------------------------------
	// creating translation storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
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
	
	function link_storage(){
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
	
	//------------------------------------------------------------------
	// adding sense
	//------------------------------------------------------------------
	
	function add($parent_node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// inserting new node
		
		$node_id = $this->data->access('node')->add();
		
		if($node_id === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// inserting new entry
		
		$query =
			'INSERT senses (node_id, parent_node_id, `order`)' .
			' SELECT ' .
			'  last_insert_id() AS node_id,' .
			"  $parent_node_id AS parent_node_id," .
			'  MAX(new_order) AS `order`' .
			' FROM (' .
			'  SELECT MAX(`order`) + 1 AS new_order' .
			'   FROM senses' .
			"   WHERE parent_node_id = $parent_node_id" .
			'   GROUP BY parent_node_id' .
			'  UNION SELECT 1 AS new_order' .
			' ) s' .
			';';
		
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// committing transaction
		
		$this->database->commit_transaction();
		
		return $node_id;
	}

	//------------------------------------------------------------------
	// hydrate a node with senses
	//------------------------------------------------------------------
	// todo:
	//  - replacing public sense_depth from MySQL_Data
	//  - separate acquiring order labels into order label mapper
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Senses $node){
		
		$this->data->sense_depth++;
		
		$order_labels_query =
			'SELECT ol.order, ol.label' .
			' FROM' .
			'  order_label_system_assignments olsa,' .
			'  order_labels ol' .
			' WHERE olsa.order_label_system_id = ol.order_label_system_id' .
			'  AND olsa.element = \'sense\'' .
			"  AND olsa.depth = {$this->data->sense_depth}"
		;
		$query =
			'SELECT s.*, ol.label AS order_label' .
			' FROM' .
			'  senses s' .
			'   LEFT JOIN (' . $order_labels_query . ') ol' .
			'    ON ol.order = s.order' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY s.`order`' .
			';';
		$senses_result = $this->database->fetch_all($query);
		
		foreach($senses_result as $sense_result){
			$sense = $node->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_node_id($sense_result['node_id']);
			$sense->set_label($sense_result['order_label']);
			$this->pull_children($sense);
		}
		
		$this->data->sense_depth--;
		
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_children(Sense $sense){
		
		// context
		$this->data->access('context')->hydrate_node($sense);
		
		// headword_node
		$this->data->access('node')->pull_headword_node_children($sense);
		
		// phrases
		$this->data->access('phrase')->hydrate_node($sense);
		
		// senses
		$this->data->access('sense')->hydrate_node($sense);
		
		return $sense;
		
	}
	
	//------------------------------------------------------------------
	// moving sense up
	//------------------------------------------------------------------
	
	function move_up($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving translation down
	//------------------------------------------------------------------
	
	function move_down($node_id){
		
		$query =
			'UPDATE senses s1, senses s2' .
			' SET' .
			'  s1.order = s2.order,' .
			'  s2.order = s1.order' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s1.order = s2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) return false;
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// getting sense depth
	//------------------------------------------------------------------
	// this relatively slow recursive solution shows that the way
	// the tree structure was implemented is a bit cumbersome
	
	function get_depth($node_id){
		$current_node_id = $node_id;
		$depth = 0;
		
		while($current_node_id){
			$query =
				'SELECT parent_node_id' .
				' FROM' .
				'  senses' .
				' WHERE' .
				"  node_id = $current_node_id".
				';';
			$result = $this->database->fetch_one($query);
			
			if($result === false) return false;
			
			if(empty($result)){
				$current_node_id = false;
			} else {
				$current_node_id = $result['parent_node_id'];
				$depth++;
			}
		}
		
		return $depth;
	}
	
	//------------------------------------------------------------------
	// getting sense label
	//------------------------------------------------------------------
	
	function get_label($node_id){
		
		$depth = $this->get_depth($node_id);
		
		$query =
			'SELECT ol.label' .
			' FROM ' .
			'  order_labels ol,' .
			'  order_label_system_assignments olsa,' .
			'  senses s' .
			" WHERE s.node_id = $node_id" .
			'  AND s.order = ol.order' .
			'  AND ol.order_label_system_id = olsa.order_label_system_id' .
			'  AND olsa.element = \'sense\''.
			"  AND olsa.depth = $depth" .
			';';
		$result = $this->database->fetch_one($query);
		
		if($result === false) return false;
		
		$sense_label = $result['label'];
		
		return $sense_label;
	}
	
	//------------------------------------------------------------------
	// deleting sense
	//------------------------------------------------------------------
	
	function delete($node_id){
		
		// starting transaction
		
		$this->database->start_transaction();
		
		// moving senses with greater order
		
		$query =
			'UPDATE senses s1, senses s2, senses s3' .
			' SET ' .
			'  s2.order = s2.order - 1' .
			" WHERE s1.node_id = $node_id" .
			'  AND s1.parent_node_id = s2.parent_node_id' .
			'  AND s2.order > s1.order' .
			'  AND s3.parent_node_id = s2.parent_node_id' .
			'  AND s3.order = s2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// deleting node
		
		$result = $this->data->access('node')->delete($node_id);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// committing transaction
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}
