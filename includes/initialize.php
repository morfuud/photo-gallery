<?php

/*
 __________________________________________________________
|+ Initialize files                                        |
------------------------------------------------------------
|- Define the core paths                                   |
|- Define them as absolute paths to make sure that         |
|  require_once works as expected.                         |
|- DIRECTORY_SEPARATOR is a PHP pre_defined constant       |
|- (\ for Windows, / for Unix)                             |
|__________________________________________________________|
*/
defined('DS') ? NULL : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? NULL :
    define('SITE_ROOT', DS.'xampp'.DS.'htdocs'.DS.'2018'.DS.'photo_gallery');

defined('LIB_PATH') ? NULL : define('LIB_PATH', SITE_ROOT.DS.'includes');

// load config file first
require_once(LIB_PATH.DS."config.php");

// load basic functions next so that everythin after can use them
require_once(LIB_PATH.DS."functions.php");

// load core objects
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."database.php");
require_once(LIB_PATH.DS."pagination.php");

// load database-related classes
require_once(LIB_PATH.DS."user.php");
require_once(LIB_PATH.DS."photograph.php");
require_once(LIB_PATH.DS."comment.php");

?>