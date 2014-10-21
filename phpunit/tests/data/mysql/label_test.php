<?php

require_once __DIR__ . '/mapper_test.php';

abstract class Label_Test extends Mapper_Test{
	
	protected static $label_name;
	protected static $labels_name;
	
	/** @var Dictionary\MySQL_Node $node_access */
	protected $node_access;
	
	/** @var Dictionary\MySQL_Label $label_access */
	protected $label_access;
	
	public function setup(){
		parent::setup();
		
		$this->node_access   = $this->data->access('node');
		$this->label_access  = $this->data->access(static::$label_name);
		
		$this->node_access->create_storage();
		$this->label_access->create_storage();
		$this->label_access->link_storage();
		
	}
	
	public function fill(){
		$node_id = $this->node_access->add();
		$this->label_access->set($node_id, 'label 1');
		
		$node_id = $this->node_access->add();
		$this->label_access->set($node_id, 'label 2');
		
	}
	
	public function test_creating(){
		$this->fill();
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 1,
				'label'                      => 'label 1',
			],
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 1,
				'parent_node_id'                       => 1,
				static::$label_name . '_id'            => 1,
			],
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			]
		]);
	}
	
	public function test_setting_old_value(){
		$this->fill();
		$this->label_access->set(1, 'label 2');
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 1,
				'parent_node_id'                       => 1,
				static::$label_name . '_id'            => 2,
			],
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			]
		]);
	}
	
	public function test_setting_new_value(){
		$this->fill();
		$this->label_access->set(1, 'label 3');
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
			[
				static::$label_name . '_id'  => 3,
				'label'                      => 'label 3',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 1,
				'parent_node_id'                       => 1,
				static::$label_name . '_id'            => 3,
			],
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			],
		]);
	}
	
	function test_updating_with_old_values(){
		$this->fill();
		$this->label_access->update(1, 'label 2');
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 1,
				'parent_node_id'                       => 1,
				static::$label_name . '_id'            => 2,
			],
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			],
		]);
	}
	
	function test_updating_with_new_values(){
		$this->fill();
		$this->label_access->update(1, 'label 3');
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
			[
				static::$label_name . '_id'  => 3,
				'label'                      => 'label 3',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 1,
				'parent_node_id'                       => 1,
				static::$label_name . '_id'            => 3,
			],
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			],
		]);
	}
	
	public function test_deleting(){
		$this->fill();
		$this->label_access->delete(1);
		
		$this->assert_table_content(static::$labels_name, [
			[
				static::$label_name . '_id'  => 2,
				'label'                      => 'label 2',
			],
		]);
		
		$this->assert_table_content('node_' . static::$labels_name, [
			[
				'node_' . static::$label_name . '_id'  => 2,
				'parent_node_id'                       => 2,
				static::$label_name . '_id'            => 2,
			]
		]);
		
	}
	
	function teardown(){
		$query = 'DROP TABLE IF EXISTS `node_' . static::$labels_name . '`;';
		$this->database->query($query);
		
		$query = 'DROP TABLE IF EXISTS `' . static::$labels_name . '`;';
		$this->database->query($query);
		
		$query = 'DROP TABLE IF EXISTS `nodes`;';
		$this->database->query($query);
	}
}
