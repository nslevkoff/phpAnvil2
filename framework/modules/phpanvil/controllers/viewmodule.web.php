<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       View_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to view module for an phpAnvil.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        View_Module phpAnvil_Controllers
*/

class ViewModuleWebAction extends BaseWebAction {

	public $moduleID = 0;


	function loadModules() {
		global $phpAnvil;

		$this->moduleID = isset($_GET['i']) ? $_GET['i'] : 0;

		$phpAnvil->loadModule($this->moduleID);
		$phpAnvil->loadWidget(MODULE_UI, 'ui');
		$phpAnvil->loadWidget(MODULE_UI, 'grid');
	}


	function process() {
		global $phpAnvil, $modules, $moduleCodes;
		global $firePHP;

		$this->enableTrace();

		$renderPage = true;


		$objModule = new ModuleModel($phpAnvil->db, $this->moduleID);
		$objModule->load();


		$backURL = 'phpAnvil/Modules';
		$returnURL = 'phpAnvil/ViewModule?i=' . $this->moduleID;

		if(isset($_GET['r']))
        {
			$moduleName = $moduleCodes[$this->moduleID];

			$phpAnvil->installModule($moduleName);
			$phpAnvil->processNewAction(SOURCE_TYPE_USER, MODULE_INSTALL, ACTION_CREATE_ALL_FILES, null);

			$phpAnvil->actionMsg->add($moduleName . ' module reinstalled.');

			$renderPage = false;
			header('Location: ' . $phpAnvil->site->webPath . $returnURL);
		} else if (isset($_GET['u']))
        {
            $moduleName = $moduleCodes[$this->moduleID];

            $phpAnvil->uninstallModule($moduleName);
            $phpAnvil->processNewAction(SOURCE_TYPE_USER, MODULE_INSTALL, ACTION_CREATE_ALL_FILES, null);

            $phpAnvil->actionMsg->add($moduleName . ' module uninstalled.');

            $renderPage = false;
            header('Location: ' . $phpAnvil->site->webPath . $backURL);
        }


		if ($renderPage) {
			//---- UI
			$UI = new UIWidget();
			$UI->menu->selected = MenuWidget::TAB_OPTIONS;
			$UI->pageIcon = 'iModule.png';
			$UI->pageTitle = $moduleCodes[$this->moduleID] . ' Module';
			$UI->pageTitleRight = 'v' . $objModule->version;
            $UI->preScript = 'js/phpanvil/phpanvil.js';

			$UI->pageNavText = 'All Modules';
			$UI->pageNavPath = $backURL;

			//----------------- Content -----------------
			$panel = new PanelWidget(null, 'Module Actions');
			$UI->content->addControl($panel);

			$sql = 'SELECT * FROM ' . SQL_TABLE_ACTIONS;
			$sql .= ' WHERE module_id=' . $this->moduleID;
			$sql .= ' ORDER BY constant';

			$objRS = $phpAnvil->db->execute($sql);

			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$objGridWidget = new GridWidget();
					$objGridWidget->icon = 'iAction_24.png';
					$objGridWidget->title = $objRS->data('constant');

					$objGridWidget->addControl(new atLiteral('', $objRS->data('name') . "<br />\n"));

					$objGridWidget->addControl(new atLiteral('', '<span class="dimBulletID">' . $objRS->data('action_id') . '</span>'));

					$UI->content->addControl($objGridWidget);
				}
				$objRS->close();
			} else {
				$phpAnvil->pageMsg->add('No actions found.');
			}


			#================= RIGHT COLUMN ==================
			$UI->actions->addControl(new ActionButtonWidget('idReinstallModule', 'btnRefresh', 'Re-Install Module', $phpAnvil->site->webPath . $returnURL . '&r=Y'));
            $UI->actions->addControl(new ActionButtonWidget('idUninstallModule', 'btnDelete', 'Uninstall Module', $phpAnvil->site->webPath . $returnURL . '&u=Y'));

			//----------------- Other Actions -----------------
//			$panel = new PanelWidget(null, 'Other Actions', 'panelRight');
//			$objList = new atList(null, atList::TYPE_BULLET, 'menuRight');
//			$objList->addControl(new atLink(null, 'Register Promotion Actions', $phpAnvil->site->webPath . $returnURL . '&pa=Y', 'bulletAdd'));
//			$panel->addControl($objList);
//			$UI->rightColumn->addControl($panel);

			//---- Finalize and Display
			$UI->display();
		}
	}
}


$objWebAction = new ViewModuleWebAction();

?>
