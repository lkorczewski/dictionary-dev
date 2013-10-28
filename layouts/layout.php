<?php

namespace Dictionary;

require_once __DIR__ . '/../dictionary.php';

require_once __DIR__ . '/../entry.php';
require_once __DIR__ . '/../sense.php';
require_once __DIR__ . '/../phrase.php';

require_once __DIR__ . '/../headword.php';
require_once __DIR__ . '/../category_label.php';
require_once __DIR__ . '/../form.php';
require_once __DIR__ . '/../context.php';
require_once __DIR__ . '/../translation.php';

interface Layout {
	
	function parse_entry(\Entry $entry);
	function parse_sense(\Sense $sense);
	function parse_phrase(\Phrase $phrase);
	function parse_headword(\Headword $headword);
	function parse_category_label(\Category_Label $category_label);
	function parse_form(\Form $form);
	function parse_context(\Context $context);
	function parse_translation(\Translation $translation);
	
}

?>
