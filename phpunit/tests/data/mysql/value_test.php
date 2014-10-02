<?php

use Database\Database;
use Dictionary\MySQL_Data;
use Dictionary\MySQL_Node;

abstract class MySQL_Value_Test extends PHPUnit_Framework_TestCase {
	
	protected $value_name;
	protected $values_name;
	
	/** @var  Database */
	protected $database;
	
	/** @var  MySQL_Data */
	protected $data;
	
	/** @var  MySQL_Node */
	protected $node_access;
	
	/** @var  Dictionary\MySQL_Multiple_Value */
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
	
	function fill(){
		$node_id = $this->node_access->add();
		$this->value_access->add($node_id, "test {$this->value_name} 1");
		$this->value_access->add($node_id, "test {$this->value_name} 2");
	}
	
	function test_creating(){
		$this->fill();
		$query = "SELECT * FROM `{$this->values_name}`;";
		$result = $this->database->fetch_all($query);
		$this->assertEquals($result, [
			[
				"{$this->value_name}_id"  => 1,
				'parent_node_id'          => 1,
				'order'                   => 1,
				$this->value_name         => "test {$this->value_name} 1",
			],
			[
				"{$this->value_name}_id"  => 2,
				'parent_node_id'          => 1,
				'order'                   => 2,
				$this->value_name         => "test {$this->value_name} 2",
			],
		]);
	}
	
//	function test_fetching_for_node(){
//		$this->fill();
//		
//		$mockNode = $this->getMockBuilder($this->values_name . '_Node_Trait_And_Interface')
//			->getMock();
//		
//		$mockNode
//			->expects($this->any())
//			->method('get_node_id')
//			->will($this->returnValue(1));
//		
//		$class = '\Dictionary\\' . $this->value_name;
//		$mockNode
//			->expects($this->any())
//			->method('add_headword')
//			->will($this->returnValue(new $class($this)));
//		
//		/*
//		$node = $this->getMockForAbstractClass(ucfirst($this->value_name) . '_Node_Trait_And_Interface');
//		*/
//		
//		$this->value_access->fetch_for_node($node);
//		$query = "SELECT * FROM `{$this->values_name}`;";
//		$result = $this->database->fetch_all($query);
//		
//		$this->assertEquals($result, [
//			[
//				"{$this->value_name}_id"  => 1,
//				'parent_node_id'          => 1,
//				'order'                   => 1,
//				$this->value_name         => "test {$this->value_name} 1",
//			],
//			[
//				"{$this->value_name}_id"  => 2,
//				'parent_node_id'          => 1,
//				'order'                   => 2,
//				$this->value_name         => "test {$this->value_name} 2",
//			],
//		]);
//	}
	
	function test_updating(){
		$this->fill();
		$this->value_access->update(2, "updated test {$this->value_name} 2");
		$query = "SELECT * FROM `{$this->values_name}`;";
		$result = $this->database->fetch_all($query);
		
		$this->assertEquals($result, [
			[
				"{$this->value_name}_id"  => 1,
				'parent_node_id'          => 1,
				'order'                   => 1,
				$this->value_name         => "test {$this->value_name} 1",
			],
			[
				"{$this->value_name}_id"  => 2,
				'parent_node_id'          => 1,
				'order'                   => 2,
				$this->value_name         => "updated test {$this->value_name} 2",
			],
		]);
	}
	
	function test_moving_up(){
		$this->fill();
		$this->value_access->move_up(2);
		
		$query = "SELECT * FROM `{$this->values_name}`;";
		$result = $this->database->fetch_all($query);
		$this->assertEquals($result, [
			[
				"{$this->value_name}_id"  => 1,
				'parent_node_id'          => 1,
				'order'                   => 2,
				$this->value_name         => "test {$this->value_name} 1",
			],
			[
				"{$this->value_name}_id"  => 2,
				'parent_node_id'          => 1,
				'order'                   => 1,
				$this->value_name         => "test {$this->value_name} 2",
			],
		]);
		
		$result = $this->data->access('headword')->move_up(2);
		$this->assertEquals($result, 0);
	}
	
	function test_moving_down(){
		$this->fill();
		$this->value_access->move_down(1);
		
		$query = "SELECT * FROM `{$this->values_name}`;";
		$result = $this->database->fetch_all($query);
		$this->assertEquals($result, [
			[
				"{$this->value_name}_id"  => 1,
				'parent_node_id'          => 1,
				'order'                   => 2,
				$this->value_name         => "test {$this->value_name} 1",
			],
			[
				"{$this->value_name}_id"  => 2,
				'parent_node_id'          => 1,
				'order'                   => 1,
				$this->value_name         => "test {$this->value_name} 2",
			],
		]);
		
		$result = $this->data->access('headword')->move_down(1);
		$this->assertEquals($result, 0);
	}
	
	function test_deleting(){
		$this->fill();
		$this->data->access($this->value_name)->delete(1);
		
		$query = "SELECT * FROM `{$this->values_name}`;";
		$result = $this->database->fetch_all($query);
		$this->assertEquals($result, [
			[
				"{$this->value_name}_id"  => 2,
				'parent_node_id'          => 1,
				'order'                   => 1,
				$this->value_name         => "test {$this->value_name} 2",
			],
		]);
	}
	
	function teardown(){
		$query = "DROP TABLE IF EXISTS `{$this->values_name}`;";
		$this->database->query($query);
		
		$query = 'DROP TABLE IF EXISTS `nodes`;';
		$this->database->query($query);
	}
}
