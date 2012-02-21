<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Build_Config_Module phpAnvil_Controllers
*/

require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to list dictionaries section for an phpAnvil.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Build_Config_Module phpAnvil_Controllers
*/

class BuildConfigWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

	}


	function process() {
		global $phpAnvil, $modules;
		global $firePHP;

		$this->enableTrace();

		$phpAnvil->processNewAction(SOURCE_TYPE_USER, MODULE_PHPANVIL, ACTION_BUILD_CONFIG_FILE, null);

		$phpAnvil->actionMsg->add('Config file (build.config.php) built successfully.');

		header('Location: ' . $phpAnvil->site->webPath . $phpAnvil->defaultWebModule . '/' . $phpAnvil->defaultWebAction);
	}
}


$objWebAction = new BuildConfigWebAction();

?>
