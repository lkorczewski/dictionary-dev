<?php

namespace Dictionary;

require_once 'database/database.php';
require_once __DIR__.'/data.php';

require_once __DIR__.'/dictionary.php';
require_once __DIR__.'/node.php';
require_once __DIR__.'/headword_node.php';
require_once __DIR__.'/entry.php';
require_once __DIR__.'/sense.php';
require_once __DIR__.'/translation.php';

require_once __DIR__.'/data/mysql/node.php';
require_once __DIR__.'/data/mysql/entry.php';
require_once __DIR__.'/data/mysql/sense.php';
require_once __DIR__.'/data/mysql/phrase.php';
require_once __DIR__.'/data/mysql/headword.php';
require_once __DIR__.'/data/mysql/category_label.php';
require_once __DIR__.'/data/mysql/form.php';
require_once __DIR__.'/data/mysql/context.php';
require_once __DIR__.'/data/mysql/translation.php';

require_once __DIR__.'/data/mysql/order_label.php';

class MySQL_Data implements Data {
	use MySQL_Node;
	use MySQL_Entry;
	use MySQL_Sense;
	use MySQL_Phrase;
	use MySQL_Headword;
	use MySQL_Category_Label;
	use MySQL_Form;
	use MySQL_Context;
	use MySQL_Translation;
	use MySQL_Order_Label;
	
	private $database;
	
	private $sense_depth = 0;

	//------------------------------------------------------------------
	// constructor
	//------------------------------------------------------------------
	
	function __construct($database){
		
		$this->database = $database;
		
	}
	
	//------------------------------------------------------------------
	// creating storage (database)
	//------------------------------------------------------------------
	
	public function create_storage(&$log){
		$methods = array(
			'create_node_storage',
			'create_entry_storage',
			'create_sense_storage',
			'create_phrase_storage',
			'create_headword_storage',
			'create_category_label_storage',
			'create_form_storage',
			'create_context_storage',
			'create_translation_storage',
			'create_order_label_storage',
			
			'link_entry_storage',
			'link_sense_storage',
			'link_phrase_storage',
			'link_headword_storage',
			'link_category_label_storage',
			'link_form_storage',
			'link_context_storage',
			'link_translation_storage',
			'link_order_label_storage',

			'fill_order_label_storage',
		);
		
		$log = array();
		
		foreach($methods as $method){
			$result = $this->$method();
			$log[] = array(
				'action' => $method,
				'result' => $result
			);
			if(!$result){
				return false;
			}
		}
		
		return true;
	}
	
	//------------------------------------------------------------------
	// pulling list of headwords
	//------------------------------------------------------------------
	
	public function pull_headwords($mask = '', $number = NULL){
		
		$mask_sql = $mask ? "  AND h.headword LIKE '$mask%'" : '';
		$number_sql = $number ? " LIMIT $number" : '';
		
		$query =
			'SELECT h.headword' .
			' FROM entries e, headwords h' .
			' WHERE e.node_id = h.parent_node_id' .
			'  AND h.order = 1' .
			$mask_sql .
			' ORDER BY `headword`' .
			$number_sql .
			';';
		$result = $this->database->fetch_all($query);
		
		$headwords = array();
		
		foreach($result as $row){
			$headwords[] = $row['headword'];
		}
		
		return $headwords;
	}
	
	//------------------------------------------------------------------
	// pulling entry from database
	//------------------------------------------------------------------
	
	public function pull_entry(Dictionary $dictionary, $headword){
		// to do: only the first headword if all are the same
		
		$query =
			'SELECT e.*' .
			' FROM headwords h, entries e' .
			" WHERE h.headword = '{$this->database->escape_string($headword)}'" .
			'  AND e.node_id = h.parent_node_id' .
			';';
		$entry_result = $this->database->fetch_one($query);
		
		// poniższe do poprawki
		if($entry_result == false || count($entry_result) == 0){
			return false;
		}
		
		$entry = new Entry($dictionary);
		
		$entry->set_id($entry_result['entry_id']);
		$entry->set_node_id($entry_result['node_id']);
		
		$this->pull_entry_children($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling entries from database by headword
	//------------------------------------------------------------------
	
	public function pull_entries(Dictionary $dictionary, $headword){
		
		$query =
			'SELECT DISTINCT e.*' .
			' FROM headwords h, entries e' .
			" WHERE h.headword = '{$this->database->escape_string($headword)}'" .
			'  AND e.node_id = h.parent_node_id' .
			';';
		$entries_result = $this->database->fetch_all($query);
		
		// poniższe do poprawki
		if($entries_result == false || count($entries_result) == 0){
			return false;
		}
		
		$entries = array();
		
		foreach($entries_result as $entry_result){
			$entry = new Entry($dictionary);
			
			$entry->set_id($entry_result['entry_id']);
			$entry->set_node_id($entry_result['node_id']);
			
			$this->pull_entry_children($entry);
			
			$entries[] = $entry;
		}
		
		return $entries;
		
	}
	
	//------------------------------------------------------------------
	// pulling entry children from database
	//------------------------------------------------------------------
	
	private function pull_entry_children(Entry $entry){
		
		$this->_pull_headwords($entry);
		$this->_pull_pronuntiations($entry);
		
		$this->pull_headword_node_children($entry);
		
		$this->_pull_phrases($entry);
		$this->_pull_senses($entry);
		
		return $entry;
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_sense_children(Sense $sense){
		
		// context
		
		$query =
			'SELECT *' .
			' FROM contexts i' .
			" WHERE i.parent_node_id = {$sense->get_node_id()}" .
			';';
		$context_result = $this->database->fetch_one($query);
		
		if(is_array($context_result) && count($context_result)){
			$context = $sense->set_context();
			$context->set_id($context_result['context_id']);
			$context->set($context_result['context']);
		}
		
		// headword_node
		
		$this->pull_headword_node_children($sense);
		
		// phrases
		
		$this->_pull_phrases($sense);
		$this->_pull_senses($sense);
		
		return $sense;
		
	}
	
	//------------------------------------------------------------------
	// pulling sense children from database
	//------------------------------------------------------------------
	
	private function pull_phrase_children(Phrase $phrase){
		
		// node
		
		$this->pull_node_children($phrase);
		
	}
	
	//------------------------------------------------------------------
	// pulling headword node children from database
	//------------------------------------------------------------------
	
	private function pull_headword_node_children(Headword_Node $node){
		
		// category label
		
		$query =
			'SELECT *' .
			' FROM node_category_labels ncl, category_labels cl' .
			' WHERE ncl.category_label_id = cl.category_label_id' .
			"  AND parent_node_id = {$node->get_node_id()}" .
			';';
		$category_label_result = $this->database->fetch_one($query);
		
		if(is_array($category_label_result) && count($category_label_result)){
			$category_label = $node->set_category_label();
			$category_label->set_id($category_label_result['category_label_id']);
			$category_label->set($category_label_result['label']);
		}
		
		// forms
		
		$query = 
			'SELECT *' .
			' FROM forms' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$forms_result = $this->database->fetch_all($query);
		
		foreach($forms_result as $form_result){
			$form = $node->add_form();
			$form->set_id($form_result['form_id']);
			$form->set_label($form_result['label']);
			$form->set_form($form_result['form']);
		}
		
		// node
		$this->pull_node_children($node);
		
	}
	
	//------------------------------------------------------------------
	// pulling node children from database
	//------------------------------------------------------------------
	
	private function pull_node_children(Node $node){
		
		// translations
		
		$query =
			'SELECT *' .
			' FROM translations' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$translations_result = $this->database->fetch_all($query);
		
		foreach($translations_result as $translation_result){
			$translation = $node->add_translation();
			$translation->set_id($translation_result['translation_id']);
			$translation->set_text($translation_result['text']);
		}
		
	}
	
	//==================================================================
	// pulling ...
	//==================================================================
	
	private function _pull_senses(Has_Senses_Interface $node){
	
		$this->sense_depth++;
		
		$query =
			'SELECT s.*, ol_2.label AS order_label' .
			' FROM' .
			'  senses s' .
			'   LEFT JOIN (' .
			'    SELECT ol.order, ol.label' .
			'     FROM' .
			'      order_label_system_assignments olsa,' .
			'      order_labels ol' .
			'     WHERE olsa.order_label_system_id = ol.order_label_system_id' .
			'      AND olsa.element = \'sense\'' .
			"      AND olsa.depth = {$this->sense_depth}" .
			'   ) ol_2' .
			'    ON ol_2.order = s.order' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY s.`order`' .
			';';
		$senses_result = $this->database->fetch_all($query);
		
		foreach($senses_result as $sense_result){
			$sense = $node->add_sense();
			$sense->set_id($sense_result['sense_id']);
			$sense->set_node_id($sense_result['node_id']);
			$sense->set_label($sense_result['order_label']);
			$this->pull_sense_children($sense);
		}
		
		$this->sense_depth--;
		
	}
	
	//------------------------------------------------------------------
	
	private function _pull_phrases(Has_Phrases_Interface $node){
		
		$query =
			'SELECT *' .
			' FROM phrases' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$phrases_result = $this->database->fetch_all($query);
		
		foreach($phrases_result as $phrase_result){
			$phrase = $node->add_phrase();
			$phrase->set_id($phrase_result['phrase_id']);
			$phrase->set_node_id($phrase_result['node_id']);
			$phrase->set($phrase_result['phrase']);
			$this->pull_phrase_children($phrase);
		}
		
	}
	
	//------------------------------------------------------------------
	
	private function _pull_headwords(Has_Headwords_Interface $node){
		
		$query =
			'SELECT *' .
			' FROM headwords' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$headwords_result = $this->database->fetch_all($query);
		
		foreach($headwords_result as $headword_result){
			$headword = $node->add_headword();
			$headword->set_id($headword_result['headword_id']);
			$headword->set($headword_result['headword']);
		}
		
	}
	
	//------------------------------------------------------------------
	
	private function _pull_pronuntiations(Has_Pronuntiations_Interface $node){
		
		$query =
			'SELECT *' .
			' FROM pronuntiations' .
			" WHERE parent_node_id = {$node->get_node_id()}" .
			' ORDER BY `order`' .
			';';
		$pronuntiations_result = $this->database->fetch_all($query);
		
		foreach($pronuntiations_result as $pronuntiation_result){
			$pronuntiation = $node->add_pronuntiation();
			$pronuntiation->set_id($pronuntiation_result['pronuntiation_id']);
			$pronuntiation->set($pronuntiation_result['pronuntiation']);
		}
	
	}
	
}

?>
