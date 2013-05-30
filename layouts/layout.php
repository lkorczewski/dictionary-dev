<?php

require_once 'dictionary/dictionary.php';

require_once 'dictionary/entry.php';
require_once 'dictionary/sense.php';
require_once 'dictionary/phrase.php';

require_once 'dictionary/form.php';
require_once 'dictionary/translation.php';

interface Layout {
	
	function parse_entry(Entry $entry);
	function parse_sense(Sense $sense);
	function parse_phrase(Phrase $phrase);
	function parse_form(Form $form);
	function parse_translation(Translation $translation);
	
}

?>
