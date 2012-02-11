<?php
/**
* @file
*/

define('MODULE_TYPE_FRAMEWORK', 1);
define('MODULE_TYPE_CUSTOM', 2);


//---- Prepare phpAnvil Arrays
$moduleCodes = array();
$moduleIDs = array();
$modules = array();

//---- Load Build Config File (build.config.php)
$buildConfigLoaded = false;

function loadBuildConfig()
{
    global $buildConfigLoaded;

    if (IN_PRODUCTION)
    {
        if (@include_once('build.config.php'))
        {
            $buildConfigLoaded = true;
        }
    } else {
        if(!@file_exists('build.config.php') )
        {
            echo 'can not include';
        } else {
            include_once 'build.config.php';
            $buildConfigLoaded = true;
        }
    }

    return $buildConfigLoaded;
}

loadBuildConfig();

?>
