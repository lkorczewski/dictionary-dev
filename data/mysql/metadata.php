<?php

namespace Dictionary;

// TODO: Ugly system of filling metadata array.
//  - ugly parameters to append_order_labels_metadata
// Possible improvements:
//  - $metadata as object
//  - Metadata as class

trait MySQL_Metadata {
	
	//==========================================================
	// reading metadata from database
	//==========================================================
	
	//----------------------------------------------------------
	// getting metadata
	//----------------------------------------------------------
	
	function get_metadata(){
		$metadata = [];
		
		$this->append_senses_metadata($metadata);
		
		return $metadata;
	}
	
	//----------------------------------------------------------
	// appending metadata: senses
	//----------------------------------------------------------
	
	private function append_senses_metadata(&$metadata){
		$this->append_order_label_metadata($metadata, 'sense', 'sense');
	}
	
	//----------------------------------------------------------
	// appending metadata: order labels
	//----------------------------------------------------------
	
	private function append_order_label_metadata(
		&$metadata,
		$metadata_category,
		$element
	){
		$query =
			'SELECT' .
				' olsa.depth,' .
				' ols.name AS system' .
			' FROM' .
				' order_label_system_assignments olsa,' .
				' order_label_systems ols' .
			' WHERE' .
				" olsa.element = '$element'" .
			' AND' .
				' ols.order_label_system_id = olsa.order_label_system_id' .
			';';
		$order_label_system_assignments = $this->database->fetch_all($query);
		
		if($order_label_system_assignments === false){
			return false;
		}
		
		foreach($order_label_system_assignments as $assignment){
			$metadata
				[$metadata_category]
				[$assignment['depth']]
				['order_label_system']
				= $assignment['system'];
		}
	}
	
	//==========================================================
	// writing metadata to database
	//==========================================================
	
	function set_metadata($metadata){
		
		$result = $this->set_sense_metadata($metadata);
		if($result === false){
			return false;
		}
		
		return true;
	}
	
	//----------------------------------------------------------
	// writing sense metadata to database
	//----------------------------------------------------------
	
	private function set_sense_metadata($metadata){
		
		if(isset($metadata['sense'])){
			foreach($metadata['sense'] as $depth => $sense_metadata){
				$result = $this->set_order_label_metadata(
					$sense_metadata,
					'sense',
					$depth
				);
				if($result === false){
					return false;
				}
			}
		}
		
		return true;
	}
	
	//----------------------------------------------------------
	// writing order label metadata to database
	//----------------------------------------------------------
	
	private function set_order_label_metadata($node_metadata, $element, $depth = 1){
		
		if(isset($node_metadata['order_label_system'])){
			$result = $this->set_order_label_system_assignment(
				$element,
				$depth,
				$node_metadata['order_label_system']
			);
			if($result === false){
				return false;
			}
		}
		
		return true;
	}
	
}
