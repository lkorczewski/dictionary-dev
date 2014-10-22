<?php

require_once __DIR__ . '/mapper_test.php';

abstract class MySQL_Value_Test extends Mapper_Test {
	
	protected static $value_name;
	protected static $values_name;
	
	/** @var Dictionary\MySQL_Node $node_access */
	protected $node_access;
	
	/** @var Dictionary\MySQL_Multiple_Value $value_access */
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
		$this->value_access->add($node_id, "test value 1");
		$this->value_access->add($node_id, "test value 2");
	}
	
	function test_creating(){
		$this->fill();
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				'order'                     => 1,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 1,
				'order'                     => 2,
				static::$value_name         => "test value 2",
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
		$this->value_access->update(2, "updated test value 2");
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				'order'                     => 1,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 1,
				'order'                     => 2,
				static::$value_name         => "updated test value 2",
			],
		]);
	}
	
	function test_moving_up(){
		$this->fill();
		$this->value_access->move_up(2);
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'   => 1,
				'parent_node_id'            => 1,
				'order'                     => 2,
				static::$value_name         => "test value 1",
			],
			[
				static::$value_name.'_id'   => 2,
				'parent_node_id'            => 1,
				'order'                     => 1,
				static::$value_name         => "test value 2",
			],
		]);
		
		$result = $this->value_access->move_up(2);
		$this->assertEquals($result, 0);
	}
	
	function test_moving_down(){
		$this->fill();
		$this->value_access->move_down(1);
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'  => 1,
				'parent_node_id'           => 1,
				'order'                    => 2,
				static::$value_name        => "test value 1",
			],
			[
				static::$value_name.'_id'  => 2,
				'parent_node_id'           => 1,
				'order'                    => 1,
				static::$value_name        => "test value 2",
			],
		]);
		
		$result = $this->value_access->move_down(1);
		$this->assertEquals($result, 0);
	}
	
	function test_deleting(){
		$this->fill();
		$this->value_access->delete(1);
		
		$this->assert_table_content(static::$values_name, [
			[
				static::$value_name.'_id'  => 2,
				'parent_node_id'           => 1,
				'order'                    => 1,
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
