<?php

namespace Dictionary;

require_once __DIR__.'/value.php';

class Translation extends Value {
	
	public function set_text($value){
		return $this->set($value);
	}
	
	public function get_text(){
		return $this->get();
	}
	
}

