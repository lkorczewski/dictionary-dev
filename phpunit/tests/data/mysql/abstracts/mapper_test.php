<?php

require_once __DIR__ . '/../../../../../data/mysql_data.php';

use Database\Database;
use Dictionary\MySQL_Data;

abstract class Mapper_Test extends PHPUnit_Framework_TestCase 
{
	
	/** @var  Database */
	protected $database;
	
	/** @var  MySQL_Data */
	protected $data;
	
	function setup(){
		$this->database = new Database([
			'user'      => 'test',
			'password'  => 'test',
			'database'  => 'test',
		]);
		
		$this->data = new MySQL_Data($this->database);
	}
	
	protected function assert_table_content($table, array $content){
		$query = "SELECT * FROM `$table`;";
		$labels = $this->database->fetch_all($query);
		
		$this->assertEquals($labels, $content);
	}
	
}
