<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/single_value.php';

class MySQL_Context extends MySQL_Single_Value {
	
	protected $table_name    = 'contexts';
	protected $element_name  = 'context';
	
	//------------------------------------------------------------------
	// hydrate a node with context 
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Context $node){
		
		$query =
			'SELECT *' .
			' FROM contexts i' .
			" WHERE i.parent_node_id = {$node->get_node_id()}" .
			';';
		$context_result = $this->database->fetch_one($query);
		
		if(is_array($context_result) && count($context_result)){
			$context = $node->set_context();
			$context->set_id($context_result['context_id']);
			$context->set($context_result['context']);
		}
		
	}
	
}
