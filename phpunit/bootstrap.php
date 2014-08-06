<?php

require __DIR__ . '/../../core/autoloader.php';

\Core\Autoloader::register(__DIR__ . '/../..');
spl_autoload_register('spl_autoload');
