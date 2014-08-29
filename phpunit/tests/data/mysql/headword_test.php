<?php

use Database\Database;
use Dictionary\MySQL_Data;
use Dictionary\MySQL_Node;
use Dictionary\MySQL_Headword;

require_once __DIR__ . '/value_test.php';

class MySQL_Headword_Test extends Value_Test {
	
//	/** @var  Database */
//	protected $database;
//	
//	/** @var  MySQL_Data */
//	protected $data;
//	
//	/** @var  MySQL_Node */
//	protected $node_access;
//	
//	/** @var  MySQL_Headword */
//	protected $headword_access;
//	
//	function setup(){
//		$this->database = new Database([
//			'user'      => 'test',
//			'password'  => 'test',
//			'database'  => 'test',
//		]);
//		
//		$this->data = new MySQL_Data($this->database);
//		
//		$this->node_access      = $this->data->access('node');
//		$this->headword_access  = $this->data->access('headword');
//		
//		$this->node_access->create_storage();
//		$this->headword_access->create_storage();
//		$this->headword_access->link_storage();
//	}
	
	function fill(){
		$node_id = $this->node_access->add();
		$this->headword_access->add($node_id, 'test headword 1');
		$this->headword_access->add($node_id, 'test headword 2');
	}
	
	function test_creating(){
		$this->fill();
		$result = $this->database->fetch_all('SELECT * FROM `headwords`;');
		$this->assertEquals($result, [
			[
				'headword_id'     => 1,
				'parent_node_id'  => 1,
				'order'           => 1,
				'headword'        => 'test headword 1',
			],
			[ 
				'headword_id'     => 2,
				'parent_node_id'  => 1,
				'order'           => 2,
				'headword'        => 'test headword 2',
			],
		]);
	}
	
	function test_updating(){
		$this->fill();
		$this->headword_access->update(2, 'updated test headword 2');
		$result = $this->database->fetch_all('SELECT * FROM `headwords`;');
			
		$this->assertEquals($result, [
			[
				'headword_id'     => 1,
				'parent_node_id'  => 1,
				'order'           => 1,
				'headword'        => 'test headword 1',
			],
			[
				'headword_id'     => 2,
				'parent_node_id'  => 1,
				'order'           => 2,
				'headword'        => 'updated test headword 2',
			],
		]);
	}
	
	function test_moving_up(){
		$this->fill();
		$this->headword_access->move_up(2);
		
		$result = $this->database->fetch_all('SELECT * FROM `headwords`;');
		$this->assertEquals($result, [
			[
				'headword_id'     => 1,
				'parent_node_id'  => 1,
				'order'           => 2,
				'headword'        => 'test headword 1',
			],
			[
				'headword_id'     => 2,
				'parent_node_id'  => 1,
				'order'           => 1,
				'headword'        => 'test headword 2',
			],
		]);
		
		$result = $this->data->access('headword')->move_up(2);
		$this->assertEquals($result, 0);
	}
	
	function test_moving_down(){
		$this->fill();
		$this->headword_access->move_down(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `headwords`;');
		$this->assertEquals($result, [
			[
				'headword_id'     => 1,
				'parent_node_id'  => 1,
				'order'           => 2,
				'headword'        => 'test headword 1',
			],
			[
				'headword_id'     => 2,
				'parent_node_id'  => 1,
				'order'           => 1,
				'headword'        => 'test headword 2',
			],
		]);
		
		$result = $this->data->access('headword')->move_down(1);
		$this->assertEquals($result, 0);
	}
	
//	function test_deleting(){
//		$this->fill();
//		$this->data->access('headword')->delete(1);
//		
//		$result = $this->database->fetch_all('SELECT * FROM `headwords`;');
//		$this->assertEquals($result, [
//			[
//				'headword_id'     => 2,
//				'parent_node_id'  => 1,
//				'order'           => 1,
//				'headword'        => 'test headword 2',
//			],
//		]);
//	}
//	
//	function teardown(){
//		$this->database->query('DROP TABLE IF EXISTS `headwords`;');
//		$this->database->query('DROP TABLE IF EXISTS `nodes`;');
//	}
	
}
