<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/single_label.php';

class MySQL_Category_Label extends MySQL_Single_Label {
	
	protected $table_name    = 'category_labels';
	protected $element_name  = 'category_label';
	
	//------------------------------------------------------------------
	// hydrating a node with category label
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Category_Label $node){
		
		$query =
			'SELECT *' .
			' FROM node_category_labels ncl, category_labels cl' .
			' WHERE ncl.category_label_id = cl.category_label_id' .
			"  AND parent_node_id = {$node->get_node_id()}" .
			';';
		$category_label_result = $this->database->fetch_one($query);
		
		if(is_array($category_label_result) && count($category_label_result)){
			$category_label = $node->set_category_label();
			$category_label->set_id($category_label_result['node_category_label_id']);
			$category_label->set($category_label_result['label']);
		}
	}
	
}
