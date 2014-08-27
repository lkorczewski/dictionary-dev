<?php

namespace Dictionary;

require_once 'database/database.php';

use \Database\Database;

abstract class MySQL_Mapper {
	
	protected $database;
	protected $data;
	
	function __construct(Database $database, MySQL_Data $data){
		$this->database  = $database;
		$this->data      = $data;
	}
} 