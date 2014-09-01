<?php

require_once __DIR__ . '/value_test.php';

//require_once __DIR__ . '/../../../../traits/has_headwords.php';
//
//abstract class Headword_Node_Trait_and_Interface implements Dictionary\Node_With_Headwords {
//	use Dictionary\Has_Headwords;
//}

class MySQL_Headword_Test extends MySQL_Value_Test {
	
	protected $value_name = 'headword';
	
}
