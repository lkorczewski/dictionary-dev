<?php

namespace Dictionary;

//----------------------------------------------------------------------------

require_once __DIR__ . '/node_interface.php';

interface Node_With_Category_Label extends Node_Interface {
	public function set_category_label();
	public function get_category_label();
}

//----------------------------------------------------------------------------

require_once __DIR__ . '/../category_label.php';

trait Has_Category_Label {
	
	private $category_label = false;
	
	//------------------------------------------------
	// category label management
	//------------------------------------------------
	
	public function set_category_label(){
		$category_label = new Category_Label($this->dictionary);
		$this->category_label = $category_label;
		
		return $category_label;
	}
	
	public function get_category_label(){
		$category_label = $this->category_label;
		
		return $category_label;
	}

}

?>
