<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/mapper.php';

class MySQL_Form extends MySQL_Mapper {
	
	//------------------------------------------------------------------
	// creating form storage (table)
	//------------------------------------------------------------------
	
	function create_storage(){
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
	
	function link_storage(){
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
	
	//------------------------------------------------------------------
	// hydrate a node with forms
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Forms $node){
		
		$query =
			'SELECT *' .
			' FROM forms' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$forms_result = $this->database->fetch_all($query);
		
		foreach($forms_result as $form_result){
			$form = $node->add_form();
			$form->set_id($form_result['form_id']);
			$form->set_label($form_result['label']);
			$form->set_form($form_result['form']);
		}
		
	}
	
	//------------------------------------------------------------------
	// creating form
	//------------------------------------------------------------------
	
	function add($parent_node_id, $label = '', $form = ''){
		
		// inserting new form
		
		$query =
			'INSERT forms (parent_node_id, `order`, label, form)' .
			' SELECT' .
			"  $parent_node_id as parent_node_id," .
			'  MAX(new_order) AS `order`,' .
			"  '$label' AS label," .
			"  '$form' AS form" .
			'  FROM (' .
			'   SELECT MAX(`order`) + 1 AS new_order' .
			'    FROM forms' .
			"    WHERE parent_node_id = $parent_node_id" .
			'    GROUP BY parent_node_id' .
			'   UNION SELECT 1 AS new_order' .
			'  ) f' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false) {
			return false;
		}
		
		// obtaining new form id
		
		$query = 'SELECT last_insert_id() AS form_id;';
		$result = $this->database->fetch_one($query);
		
		if($result === false){
			return false;
		}
		
		$form_id = $result['form_id'];
		
		return $form_id;
	}
	
	//------------------------------------------------------------------
	// updating form
	//------------------------------------------------------------------
	
	function update($form_id, $label, $form){
		
		$query =
			'UPDATE forms' .
			' SET' .
			"  label = '$label', " .
			"  form = '$form'" .
			" WHERE form_id = $form_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving form up
	//------------------------------------------------------------------
	
	function move_up($form_id){
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET' .
			'  f1.order = f2.order,' .
			'  f2.order = f1.order' .
			" WHERE f1.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order = f2.order + 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// moving form down
	//------------------------------------------------------------------
	
	function move_down($form_id){
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET' .
			'  f1.order = f2.order,' .
			'  f2.order = f1.order' .
			" WHERE f1.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order = f2.order - 1' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			return false;
		}
		
		$affected_rows = $this->database->get_affected_rows();
		
		return $affected_rows;
	}
	
	//------------------------------------------------------------------
	// deleting translation
	//------------------------------------------------------------------
	
	function delete($form_id){
		
		// it should be much simpler
		// maybe combined queries
		// maybe the translation should be called by order, not id
		
		$this->database->start_transaction();
		
		// the order of operations doesn't permit
		// a unique (sense_id, order) key that would be useful otherwise
		// maybe deleting by order would be better
		
		// moving forms with greater order
		
		$query =
			'UPDATE forms f1, forms f2' .
			' SET f1.order = f1.order - 1' .
			" WHERE f2.form_id = $form_id" .
			'  AND f1.parent_node_id = f2.parent_node_id' .
			'  AND f1.order > f2.order' .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		// deleting translation
		
		$query =
			'DELETE FROM forms' .
			" WHERE form_id = $form_id" .
			';';
		$result = $this->database->execute($query);
		
		if($result === false){
			$this->database->rollback_transaction();
			return false;
		}
		
		$this->database->commit_transaction();
		
		return true;
	}
	
}
