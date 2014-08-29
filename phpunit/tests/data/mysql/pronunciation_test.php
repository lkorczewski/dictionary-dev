<?php

use Database\Database;
use Dictionary\MySQL_Data;
use Dictionary\MySQL_Node;
use Dictionary\MySQL_Pronunciation;

class MySQL_Pronunciation_Test extends PHPUnit_Framework_TestCase {
	
	/** @var  Database */
	protected $database;
	
	/** @var  MySQL_Data */
	protected $data;
	
	/** @var  MySQL_Node */
	protected $node_access;
	
	/** @var  MySQL_Pronunciation */
	protected $pronunciation_access;
	
	function setup(){
		$this->database = new Database([
			'user'      => 'test',
			'password'  => 'test',
			'database'  => 'test',
		]);
		
		$this->data = new MySQL_Data($this->database);
		
		$this->node_access           = $this->data->access('node');
		$this->pronunciation_access  = $this->data->access('pronunciation');
		
		$this->node_access->create_storage();
		$this->pronunciation_access->create_storage();
		$this->pronunciation_access->link_storage();
	}
	
	function fill(){
		$node_id = $this->node_access->add();
		$this->pronunciation_access->add($node_id, 'test pronunciation 1');
		$this->pronunciation_access->add($node_id, 'test pronunciation 2');
	}
	
	function test_creating(){
		$this->fill();
		$result = $this->database->fetch_all('SELECT * FROM `pronunciations`;');
		$this->assertEquals($result, [
			[
				'pronunciation_id'  => 1,
				'parent_node_id'    => 1,
				'order'             => 1,
				'pronunciation'     => 'test pronunciation 1',
			],
			[ 
				'pronunciation_id'  => 2,
				'parent_node_id'    => 1,
				'order'             => 2,
				'pronunciation'     => 'test pronunciation 2',
			],
		]);
	}
	
	function test_updating(){
		$this->fill();
		$this->pronunciation_access->update(2, 'updated test pronunciation 2');
		$result = $this->database->fetch_all('SELECT * FROM `pronunciations`;');
			
		$this->assertEquals($result, [
			[
				'pronunciation_id'  => 1,
				'parent_node_id'    => 1,
				'order'             => 1,
				'pronunciation'     => 'test pronunciation 1',
			],
			[
				'pronunciation_id'  => 2,
				'parent_node_id'    => 1,
				'order'             => 2,
				'pronunciation'     => 'updated test pronunciation 2',
			],
		]);
	}
	
	function test_moving_up(){
		$this->fill();
		$this->pronunciation_access->move_up(2);
		
		$result = $this->database->fetch_all('SELECT * FROM `pronunciations`;');
		$this->assertEquals($result, [
			[
				'pronunciation_id'  => 1,
				'parent_node_id'    => 1,
				'order'             => 2,
				'pronunciation'     => 'test pronunciation 1',
			],
			[
				'pronunciation_id'  => 2,
				'parent_node_id'    => 1,
				'order'             => 1,
				'pronunciation'     => 'test pronunciation 2',
			],
		]);
		
		$result = $this->data->access('pronunciation')->move_up(2);
		$this->assertEquals($result, 0);
	}
	
	function test_moving_down(){
		$this->fill();
		$this->pronunciation_access->move_down(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `pronunciations`;');
		$this->assertEquals($result, [
			[
				'pronunciation_id'  => 1,
				'parent_node_id'    => 1,
				'order'             => 2,
				'pronunciation'     => 'test pronunciation 1',
			],
			[
				'pronunciation_id'  => 2,
				'parent_node_id'    => 1,
				'order'             => 1,
				'pronunciation'     => 'test pronunciation 2',
			],
		]);
		
		$result = $this->data->access('pronunciation')->move_down(1);
		$this->assertEquals($result, 0);
	}
	
	function test_deleting(){
		$this->fill();
		$this->data->access('pronunciation')->delete(1);
		
		$result = $this->database->fetch_all('SELECT * FROM `pronunciations`;');
		$this->assertEquals($result, [
			[
				'pronunciation_id'  => 2,
				'parent_node_id'    => 1,
				'order'             => 1,
				'pronunciation'     => 'test pronunciation 2',
			],
		]);
	}
	
	function teardown(){
		$this->database->query('DROP TABLE IF EXISTS `pronunciations`;');
		$this->database->query('DROP TABLE IF EXISTS `nodes`;');
	}
	
}
