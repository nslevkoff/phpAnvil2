<?php
/**
*
* @file
* phpAnvil Module Controller
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          phpAnvil_Module
*/

require_once 'phpanvil.inc.php';

require_once PHPANVIL2_FRAMEWORK_PATH . 'Base.module.php';

/**
*
* phpAnvil Module Class
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          phpAnvil_Module
*/

class phpAnvilModule extends BaseModule {

	private $_configContent = '';


	function __construct() {
		$this->enableTrace();

		$return = parent::__construct();

        $this->type = self::TYPE_CORE;
        $this->name = 'phpAnvil Module';
        $this->refName = 'phpAnvil';
        $this->version = '1.0';
        $this->build = '5';

		return $return;
	}


	function addConfigContent($content) {
		global $firePHP;

		FB::log($content);

		$this->_configContent .= $content . "\n";

		return true;
	}


	public function createSourceTypeContent() {
		global $phpAnvil;
		global $firePHP;

		$return = true;

		$sql = 'SELECT *';
		$sql .= ' FROM ' . SQL_TABLE_SOURCE_TYPES;
		$sql .= ' ORDER BY source_type_id';

		$objRS = $phpAnvil->db->execute($sql);

		$content = '';

		while ($objRS->read()) {
			$constant = 'SOURCE_TYPE_' . strtoupper($objRS->data('constant'));

			$content .= "\tdefine('" . $constant . "', " . $objRS->data('source_type_id') . ");\n";
		}

		return $content;
	}


	function processAction(Action $action) {
		global $phpAnvil, $modules;
		global $firePHP;

		$return = true;

		switch ($action->type) {
			case ACTION_BUILD_CONFIG_FILE:
            CASE $phpAnvil->getActionID('phpanvil', 'BUILD_CONFIG_FILE'):
				$sql = 'SELECT *';
				$sql .= ' FROM ' . SQL_TABLE_MODULES;
				$sql .= ' ORDER BY module_id';

				$objRS = $phpAnvil->db->execute($sql);

				while ($objRS->read()) {
					$phpAnvil->loadModule($objRS->data('code'));
				}

				$this->addConfigContent('<?php' . "\n");
				$phpAnvil->processNewAction($action->source, $phpAnvil->modules['*']->id, ACTION_BUILD_CONFIG_CONTENT, null);
				$this->addConfigContent('?>');

				$filePath = APP_PATH . 'build.config.php';
				FB::log($filePath);

				$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$filePath = ' . $filePath, self::TRACE_TYPE_DEBUG);

				FB::log($this->_configContent);

				file_put_contents($filePath, $this->_configContent);

				break;

			case ACTION_BUILD_CONFIG_CONTENT:
            CASE $phpAnvil->getActionID('phpanvil', 'BUILD_CONFIG_CONTENT'):
				$modules[MODULE_PHPANVIL]->addConfigContent($this->createSourceTypeContent());
				break;

            CASE $phpAnvil->getActionID('doxygen', 'BUILD_DOXYGEN_FILES'):

                $modules[MODULE_DOXYGEN]->filePath = PHPANVIL_MODULES_PATH .
                    self::CODE . '/doxygen/';

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_SESSIONS);

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_SESSION_VARS);

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_SOURCE_TYPES);

                break;
        }

		return $return;
	}

}


$phpAnvil->module['phpanvil'] = new phpAnvilModule();
//$phpAnvil->module->add(new phpAnvilModule(), 'phpAnvil');

?>
