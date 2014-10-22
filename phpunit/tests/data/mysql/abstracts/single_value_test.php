<?php

require_once __DIR__ . '/mapper_test.php';

abstract class MySQL_Single_Value_Test extends Mapper_Test {
	
	protected static $value_name;
	protected static $values_name;
	
	/** @var Dictionary\MySQL_Node $node_access */
	protected $node_access;
	
	/** @var Dictionary\MySQL_Single_Value $value_access */
	protected $value_access;
	
	function setup(){
		parent::setup();
		
		$this->node_access   = $this->data->access('node');
		$this->value_access  = $this->data->access(static::$value_name);
		
		$this->node_access->create_storage();
		$this->value_access->create_storage();
		$this->value_access->link_storage();
		
		static::$values_name = static::$value_name . 's';
	}
	
	function fill(){
		$node_id = $this->node_access->add();
		$this->value_access->set($node_id, "test value 1");
		
		$node_id = $this->node_access->add();
		$this->value_access->set($node_id, "test value 2");
	}
	
	function test_creating(){
		$this->fill();
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 2,
				static::$value_name         => "test value 2",
			],
		]);
	}
	
	function test_setting_existing(){
		$this->fill();
		$this->value_access->set(2, "updated test value 2");
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 2,
				static::$value_name         => "updated test value 2",
			],
		]);
	}
	
	function test_updating(){
		$this->fill();
		$this->value_access->update(2, "updated test value 2");
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 2,
				static::$value_name         => "updated test value 2",
			],
		]);
	}
	
	function test_deleting(){
		$this->fill();
		$this->value_access->delete(1);
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'  => 2,
				'parent_node_id'           => 2,
				static::$value_name        => "test value 2",
			],
		]);
	}
	
	function teardown(){
		$query = 'DROP TABLE IF EXISTS `' . static::$values_name . '`;';
		$this->database->query($query);
		
		$query = 'DROP TABLE IF EXISTS `nodes`;';
		$this->database->query($query);
	}
}
