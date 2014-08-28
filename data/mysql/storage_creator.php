<?php

namespace Dictionary;

class MySQL_Storage_Creator {
	
	protected $data;
	protected $entities = [
		'node',
		
		'entry',
		'sense',
		'phrase',
		
		'headword',
		'pronunciation',
		'category_label',
		'form',
		'context',
		'translation',
		
		'order_label',
	];
	
	function __construct(MySQL_Data $data){
		$this->data = $data;
	}
	
	//------------------------------------------------------------------
	// creating storage (database)
	//------------------------------------------------------------------
	// todo: too many repetitions, but it's difficult to avoid them without exceptions
	
	function run(&$log){
		
		// allowing continuing previous log
		if(!is_array($log)){
			$log = [];
		}
		
		//--------------------------------------------------------
		
		$creation_log = $this->iterate_over_mappers('create_storage');
		
		if(!$creation_log){
			return false;
		}
		
		$log = array_merge($log, $creation_log);
		
		//--------------------------------------------------------
		
		$linking_log = $this->iterate_over_mappers('link_storage');
		
		if(!$linking_log){
			return false;
		}
		
		$log = array_merge($log, $linking_log);
		
		//--------------------------------------------------------
		
		$filling_log = $this->iterate_over_mappers('fill_storage');
		
		if(!$filling_log){
			return false;
		}
		
		$log = array_merge($log, $filling_log);
		
		//--------------------------------------------------------
		
		return true;
	}
	
	//------------------------------------------------------------------
	
	private function iterate_over_mappers($method){
		$log = [];
		
		foreach($this->entities as $entity){
			
			if(!method_exists($this->data->access($entity), $method)){
				continue;
			}
			
			$result = $this->data->access($entity)->{$method}();
			
			if($result === false){
				return false;
			}
			
			$log[] = [
				'action' => "$method @ $entity",
				'result' => $result,
			];
		}
		
		return $log;
	}
}
