<?php

use Database\Database;
use Dictionary\MySQL_Data;
use Dictionary\MySQL_Node;
use Dictionary\MySQL_Translation;

class MySQL_Translation_Test extends PHPUnit_Framework_TestCase {
	
	/** @var  Database */
	protected $database;
	
	/** @var  MySQL_Data */
	protected $data;
	
	/** @var  MySQL_Node */
	protected $node_access;
	
	/** @var  MySQL_Translation */
	protected $translation_access;
	
	function setup(){
		$this->database = new Database([
			'user'      => 'test',
			'password'  => 'test',
			'database'  => 'test',
		]);
		
		$this->data = new MySQL_Data($this->database);
		
		$this->node_access         = $this->data->access('node');
		$this->translation_access  = $this->data->access('translation');
		
		$this->node_access->create_storage();
		$this->translation_access->create_storage();
		$this->translation_access->link_storage();
	}
	
	function fill(){
		$node_id = $this->node_access->add();
		$this->translation_access->add($node_id, 'test translation 1');
		$this->translation_access->add($node_id, 'test translation 2');
	}
	
	function test_creating(){
		$this->fill();
		$result = $this->database->fetch_all('SELECT * FROM `translations`;');
		$this->assertEquals($result, [
			[
				'translation_id'  => 1,
				'parent_node_id'  => 1,
				'order'           => 1,
				'text'            => 'test translation 1',
			],
			[ 
				'translation_id'  => 2,
				'parent_node_id'  => 1,
				'order'           => 2,
				'text'            => 'test translation 2',
			],
		]);
	}
	
	function test_updating(){
		$this->fill();
		$this->translation_access->update(2, 'updated test translation 2');
		$result = $this->database->fetch_all('SELECT * FROM `translations`;');
			
		$this->assertEquals($result, [
			[
				'translation_id'  => 1,
				'parent_node_id'  => 1,
				'order'           => 1,
				'text'            => 'test translation 1',
			],
			[
				'translation_id'  => 2,
				'parent_node_id'  => 1,
				'order'           => 2,
				'text'            => 'updated test translation 2',
			],
		]);
	}
	
	function test_moving_up(){
		$this->fill();
		$this->translation_access->move_up(2);
		
		$result = $this->database->fetch_all('SELECT * FROM `translations`;');
		$this->assertEquals($result, [
			[
				'translation_id'  => 1,
				'parent_node_id'  => 1,
				'order'           => 2,
				'text'            => 'test translation 1',
			],
			[
				'translation_id'  => 2,
				'parent_node_id'  => 1,
				'order'           => 1,
				'text'            => 'test translation 2',
			],
		]);
		
		$result = $this->data->access('translation')->move_up(2);
		$this->assertEquals($result, 0);
	}
	
	function test_moving_down(){
		$this->fill();
		$this->translation_access->move_down(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `translations`;');
		$this->assertEquals($result, [
			[
				'translation_id'  => 1,
				'parent_node_id'  => 1,
				'order'           => 2,
				'text'            => 'test translation 1',
			],
			[
				'translation_id'  => 2,
				'parent_node_id'  => 1,
				'order'           => 1,
				'text'            => 'test translation 2',
			],
		]);
		
		$result = $this->data->access('translation')->move_down(1);
		$this->assertEquals($result, 0);
	}
	
	function test_deleting(){
		$this->fill();
		$this->data->access('translation')->delete(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `translations`;');
		$this->assertEquals($result, [
			[
				'translation_id'  => 2,
				'parent_node_id'  => 1,
				'order'           => 1,
				'text'            => 'test translation 2',
			],
		]);
	}
	
	function teardown(){
		$this->database->query('DROP TABLE IF EXISTS `translations`;');
		$this->database->query('DROP TABLE IF EXISTS `nodes`;');
	}
	
}
