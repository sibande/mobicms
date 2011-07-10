<?php

session_start();

require('fuuze/fuuze.php');

// __autoloads
require_once '../plugins/Twig/Autoloader.php';
Twig_Autoloader::register();

require_once '../plugins/fform/autoloader.php';
FForm_Autoloader::register();

require_once '../plugins/kil/autoloader.php';
KIL_Autoloader::register();

spl_autoload_register('autoload');

define('PROJECT_ROOT_DIR', dirname(dirname(__FILE__)));
define('APPLICATION_DIR', 'app');
define('FRAMEWORK_DIR', 'web');
define('FCONFIG_FILE_PATH', dirname(dirname(__FILE__)).'/config.php');


new Run();
