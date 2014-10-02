<?php

namespace Dictionary;

require_once __DIR__ . '/value.php';

class MySQL_Translation extends MySQL_Multiple_Value {
	
	protected $table_name    = 'translations';
	protected $element_name  = 'translation';
	
	//==================================================================
	// atomic operations: translations
	//==================================================================
	
	//------------------------------------------------------------------
	// hydrating a node with translations
	//------------------------------------------------------------------
	
	function hydrate_node(Node_With_Translations $node){
		
		$query =
			'SELECT *' .
			' FROM translations' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$translations_result = $this->database->fetch_all($query);
		
		foreach($translations_result as $translation_result){
			$translation = $node->add_translation();
			$translation->set_id($translation_result['translation_id']);
			$translation->set($translation_result['translation']);
		}
		
	}
	
}
