<?php

use Database\Database;
use Dictionary\MySQL_Data;
use Dictionary\MySQL_Node;

abstract class Value_Test extends PHPUnit_Framework_TestCase {
	
	protected $value_name;
	protected $values_name;
	
	/** @var  Database */
	protected $database;
	
	/** @var  MySQL_Data */
	protected $data;
	
	/** @var  MySQL_Node */
	protected $node_access;
	
	/** @var  Dictionary\MySQL_Headword|Dictionary\MySQL_Pronunciation */
	protected $value_access;
	
	function setup(){
		$this->database = new Database([
			'user'      => 'test',
			'password'  => 'test',
			'database'  => 'test',
		]);
		
		$this->data = new MySQL_Data($this->database);
		
		$this->node_access   = $this->data->access('node');
		$this->value_access  = $this->data->access($this->value_name);
		
		$this->node_access->create_storage();
		$this->value_access->create_storage();
		$this->value_access->link_storage();
		
		$this->values_name = "{$this->value_name}s";
	}
	
	function test_deleting(){
		$this->fill();
		$this->data->access($this->value_name)->delete(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `{$this->values_name}`;');
		$this->assertEquals($result, [
			[
				'headword_id'      => 2,
				'parent_node_id'   => 1,
				'order'            => 1,
				$this->value_name  => "test {$this->value_name} 2",
			],
		]);
	}
	
	function teardown(){
		$this->database->query("DROP TABLE IF EXISTS `{$this->values_name}`;");
		$this->database->query('DROP TABLE IF EXISTS `nodes`;');
	}
}