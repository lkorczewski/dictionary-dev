<?php

namespace Dictionary;

require_once __DIR__ . '/abstracts/multiple_value.php';

class MySQL_Pronunciation extends MySQL_Multiple_Value {
	
	protected $table_name    = 'pronunciations';
	protected $element_name  = 'pronunciation';
	
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
	
}
