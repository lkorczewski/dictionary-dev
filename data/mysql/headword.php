<?php

namespace Dictionary;

require_once __DIR__ . '/value.php';

class MySQL_Headword extends MySQL_Multiple_Value {
	
	protected $table_name    = 'headwords';
	protected $element_name  = 'headword';
	
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
	
}
