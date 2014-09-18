<?php

namespace Dictionary;

require_once __DIR__.'/value.php';

class Translation extends Value {
	
	protected static $snakized_name     = 'translation';
	protected static $camelized_name  = 'Translation';
	
}

