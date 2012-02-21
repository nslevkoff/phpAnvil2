<?php
//---- Required Special Classes
//require_once('Action.class.php');
//require_once('Module.model.php');

require_once('EventListener.class.php');

require_once('Controller.collection.php');
require_once('Plugin.collection.php');
require_once('Database.collection.php');
require_once('Module.collection.php');
require_once('Option.collection.php');
require_once('Path.collection.php');

require_once('anvilModule.abstract.php');


//---- Initiate the phpAnvil Object
require_once('phpAnvil.class.php');

$phpAnvil = new phpAnvil2();

?>
