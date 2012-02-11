<?php
/**
*
* @file
* Install Module Controller
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          Install_Module
*
*/

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.module.php');

/**
*
* Install Module Class
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          Install_Module
*
*/

class InstallModule extends BaseModule {

	const NAME			= 'Install Module';
	const CODE			= 'Install';
	const VERSION		= '1.0';
	const VERSION_BUILD = '3';
	const VERSION_DTS	= '8/19/2010 10:30:00 AM PST';


	public function __construct() {
		$this->enableTrace();

		$return = parent::__construct();

		return $return;
	}


	public function createActionsContent() {
		global $phpAnvil;

		$content = '';
        $content2 = '';


		//---- Generate Module Actions
		$sql = 'SELECT A.*, M.code AS module_code';
		$sql .= ' FROM ' . SQL_TABLE_ACTIONS . ' AS A';
        $sql .= ' LEFT JOIN ' . SQL_TABLE_MODULES . ' AS M';
        $sql .= ' ON A.module_id=M.module_id';
		$sql .= ' ORDER BY module_id, action_id';

		$objRS = $phpAnvil->db->execute($sql);


		while ($objRS->read()) {
            $moduleCode = strtolower($objRS->data('module_code'));

            if ($objRS->data('module_id') == 0)
            {
                $moduleCode = '*';
            }

			$content .= "\tdefine('ACTION_" . strtoupper($objRS->data('constant')) . "', " . $objRS->data('action_id') . ");\n";
            $content2 .= "\t\$actions['" . $moduleCode . "']['" . strtoupper($objRS->data('constant')) . "'] = " . $objRS->data('action_id') . ";\n";
		}

        $content .= "\n" . $content2;

		return $content;
	}


	public function createModulesContent() {
		global $phpAnvil;

		$sql = 'SELECT *';
		$sql .= ' FROM ' . SQL_TABLE_MODULES;
		$sql .= ' ORDER BY module_id';

		$objRS = $phpAnvil->db->execute($sql);

		$content = '';
		$content2 = '';
		$content3 = '';
		$content4 = '';

		//---- Define ALL
		$content .= "\tdefine('" . $phpAnvil->modules['*']->id . "', 0);\n";
		$content2 .= "\t\$moduleIDs['all'] = " . $phpAnvil->modules['*']->id . ";\n";
		$content3 .= "\t\$moduleCodes[" . $phpAnvil->modules['*']->id . "] = 'ALL';\n";
//		$content4 .= "\t\$moduleTypes[" . $phpAnvil->modules['*']->id . "] = 0;\n";

		while ($objRS->read())
        {
			$content .= "\tdefine('MODULE_" . strtoupper($objRS->data('code')) . "', " . $objRS->data('module_id') . ");\n";

			$content2 .= "\t\$moduleIDs['" . strtolower($objRS->data('code')) . "'] = MODULE_" . strtoupper($objRS->data('code')) . ";\n";

			$content3 .= "\t\$moduleCodes[MODULE_" . strtoupper($objRS->data('code')) . "] = '" . $objRS->data('code') . "';\n";

			$content4 .= "\t\$moduleTypes[MODULE_" . strtoupper($objRS->data('code')) . "] = " . $objRS->data('module_type_id') . ";\n";
		}

		$content .= "\n" . $content2 . "\n" . $content3 . "\n" . $content4;

		return $content;
	}


	public function createJavascriptContent() {
		global $phpAnvil;
		global $firePHP;

		$jsContent = '';

		$jsContent .= 'const SERVER_ROOT_PATH = "' . SERVER_ROOT_PATH . "\";\n";
		$jsContent .= 'const WEB_ROOT_PATH = "' . $phpAnvil->site->webPath . "\";\n";


		//---- Actions ------------------------------------
		$sql = 'SELECT *';
		$sql .= ' FROM ' . SQL_TABLE_ACTIONS;
		$sql .= ' ORDER BY action_id';

		$objRS = $phpAnvil->db->execute($sql);

		$jsContent .= 'const';
		$jsContentCount = 0;

		while ($objRS->read()) {
			if ($jsContentCount != 0) {
				$jsContent .= ',';
			} else {
				$jsContent .= ' ';
			}
			$jsContent .= 'ACTION_' . strtoupper($objRS->data('constant')) . '=' . $objRS->data('action_id');
			$jsContentCount++;
		}

		$jsContent .= ";\n";


		//---- Modules ------------------------------------
		$sql = 'SELECT *';
		$sql .= ' FROM ' . SQL_TABLE_MODULES;
		$sql .= ' ORDER BY module_id';

		$objRS = $phpAnvil->db->execute($sql);

		$jsContent .= 'const';

		//---- Define ALL
		$jsContent .= ' $phpAnvil->modules['*']->id=0';

		while ($objRS->read()) {
			$jsContent .= ', MODULE_' . strtoupper($objRS->data('code')) . '=' . $objRS->data('module_id');
		}

		$jsContent .= ";\n";

		return $jsContent;
	}

	public function createJavascriptFile() {
		global $phpAnvil;
		global $firePHP;


		$jsContent = $this->createJavascriptContent();


		//---- Save JS File --------------------------------

		$filePath = SITE_PATH . 'js/const.js';

//		FB::log($filePath);

		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, '$filePath = ' . $filePath, self::TRACE_TYPE_DEBUG);
		file_put_contents($filePath, $jsContent);

		return true;
	}



	public function processAction(Action $action) {
		global $phpAnvil, $modules;
		global $firePHP;

		switch ($action->type) {
			case ACTION_CREATE_ACTIONS_FILE:
//				$this->createActionsFile();
				$phpAnvil->processNewAction($action->source, MODULE_PHPANVIL, ACTION_BUILD_CONFIG_FILE, null);
				break;

			case ACTION_CREATE_MODULES_FILE:
//				$this->createModulesFile();
				$phpAnvil->processNewAction($action->source, MODULE_PHPANVIL, ACTION_BUILD_CONFIG_FILE, null);
				break;

			case ACTION_CREATE_ALL_FILES:
//				$this->createActionsFile();
//				$this->createModulesFile();
				$phpAnvil->processNewAction($action->source, MODULE_PHPANVIL, ACTION_BUILD_CONFIG_FILE, null);
				$this->createJavascriptFile();
				break;

			case ACTION_BUILD_CONFIG_CONTENT:
//				FB::log('Building Install Config Content...');
				$modules[MODULE_PHPANVIL]->addConfigContent($this->createActionsContent());
				$modules[MODULE_PHPANVIL]->addConfigContent($this->createModulesContent());
				break;
		}
	}

}

?>