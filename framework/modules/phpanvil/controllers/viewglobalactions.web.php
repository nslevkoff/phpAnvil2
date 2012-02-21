<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       View_Global_Actions_Module phpAnvil_Controllers
*/

require_once(PHPANVIL2_COMPONENT_PATH . 'anvilForm.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilEntry.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilHidden.class.php');


require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to view global actions for phpAnvil.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        View_Global_Actions_Module phpAnvil_Controllers
*/

class ViewGlobalActionsWebAction extends BaseWebAction {

	public $moduleID = 0;


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadWidget(MODULE_UI, 'ui');
		$phpAnvil->loadWidget(MODULE_UI, 'grid');
	}


	function process() {
		global $phpAnvil, $modules, $moduleCodes;
		global $firePHP;

		$this->enableTrace();

		$renderPage = true;

		$backURL = 'phpAnvil/Modules';
		$returnURL = 'phpAnvil/ViewGlobalActions';

		if ($renderPage) {
			//---- UI
			$UI = new UIWidget();
			$UI->menu->selected = MenuWidget::TAB_OPTIONS;
			$UI->pageIcon = 'iAction.png';
			$UI->pageTitle = 'All Modules';
			$UI->pageTitleRight = 'Global Actions';

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

					$objGridWidget->addControl(new anvilLiteral('', $objRS->data('name') . "<br />\n"));
					$objGridWidget->addControl(new anvilLiteral('', '<span class="dimBulletID">' . $objRS->data('action_id') . '</span>'));

					$UI->content->addControl($objGridWidget);
				}
				$objRS->close();
			} else {
				$phpAnvil->pageMsg->add('No actions found.');
			}

			//---- Finalize and Display
			$UI->display();
		}
	}
}

$objWebAction = new ViewGlobalActionsWebAction();
?>
