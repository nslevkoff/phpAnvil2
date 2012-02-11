<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Modules phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atComboBox.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.controller.php');

/**
* Web action to list dictionaries section for phpAnvil.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Modules phpAnvil_Controllers
*/

class ModulesController extends BaseController {


    function __construct()
    {
        parent::__construct();

        $this->module       = 'phpAnvil';
        $this->name         = 'Modules Controller';
        $this->refName      = 'modules';
        $this->version      = '1.0';
        $this->build        = '1';
        $this->copyright    =  '(c) 2010 Solutions By Design';


        return true;
    }


	function init()
    {
		global $phpAnvil;

        $return = parent::init();

		$phpAnvil->loadWidget('UI', 'ui');
		$phpAnvil->loadWidget('UI', 'panel');
		$phpAnvil->loadWidget('UI', 'grid');

        return $return;
	}


	function open()
    {
		global $phpAnvil;
		global $firePHP;

        $return = parent::open();

		$this->enableTrace();

		$renderPage = true;


		if(isset($_POST['btn'])) {
			if ($_POST['btn'] == 'Save') {
				if ($_POST['name'] != '') {
					$moduleName = $_POST['name'];
					$phpAnvil->installModule($moduleName);
					$phpAnvil->processNewAction(SOURCE_TYPE_USER, MODULE_INSTALL, ACTION_CREATE_ALL_FILES, null);

					$phpAnvil->actionMsg->add($moduleName . ' installed.');

//					$renderPage = false;
//					header('Location: ' . $phpAnvil->site->webPath . 'Site/Sites');

				} else {
					$phpAnvil->errorMsg->add('Missing module name.');
//					FB::log('Missing Site Name');
//					echo 'Missing Site Name';
				}
			}
		}


		if ($renderPage) {
			//---- UI
			$UI = new UIWidget();
//			$UI->menu->selected = MenuWidget::TAB_DEV;
			$UI->pageIcon = 'iModule.png';
			$UI->pageTitle = 'Modules';
			$UI->preScript = 'js/phpanvil/phpanvil.js';
			$UI->pageNavText = 'Dev Menu';
			$UI->pageNavPath = 'Dev/Menu';


			//----------------- Content -----------------
			$panel = new PanelWidget(null, '');
//			$objLink = new atLink(null, 'New Site', $phpAnvil->site->webPath . '#', 'idAddItem add');
//			$panel->actions->addControl($objLink);

			$sql = 'SELECT * FROM ' . SQL_TABLE_MODULES;
//			$sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
			$sql .= ' ORDER BY code';

			$objRS = $phpAnvil->db->execute($sql);



			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$objGridWidget = new GridWidget();

					$objGridWidget->icon = 'iModule_24.png';
					$objGridWidget->linkURL = $phpAnvil->site->webPath . 'phpAnvil/ViewModule?i=' . $objRS->data('module_id');
					$objGridWidget->title = '/' . $objRS->data('code');

//					$objGridWidget->addControl(new atLiteral('', $objRS->data('name') . "<br />\n"));
					$objGridWidget->addControl(new atLiteral('', $objRS->data('name') . ' v' . $objRS->data('version') . "<br />\n"));
					$objGridWidget->addControl(new atLiteral('', '<span class="dimBulletID">' . $objRS->data('module_id') . '</span>'));

					$UI->content->addControl($objGridWidget);
				}
				$objRS->close();
			} else {
				$UI->content->addControl(new atLiteral(null, '<div class="devGrid">No modules installed.</div>'));
			}


			#================= RIGHT COLUMN ==================
			$newButton = new ActionButtonWidget('idInstallModule', 'btnNew', 'Install Module', '#');
			$UI->actions->addControl($newButton);

			//----------------- Edit -----------------

			$panel = new PanelWidget('idPanelEdit', 'Install Module', 'panelEdit');
			$objForm = new atForm('idEntryForm', 'post', '', array('class' => 'idForm'), false);
			$objForm->innerTemplate = 'installModule.tpl';

			$objForm->addControl(new atEntry('idName', 'name', 20, 50));

			$newButton = new ActionButtonWidget('idSave', 'btnSave95', 'Install', '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idEntryForm\').submit();';
			$objForm->addControl($newButton);

			$newButton = new ActionButtonWidget('idCancel', 'btnCancel95', 'Cancel', '#');
			$newButton->class = 'actionFormButton';
			$objForm->addControl($newButton);


			$panel->addControl($objForm);
			$UI->actions->addControl($panel);


			//----------------- See Also -----------------
			$panel = new PanelWidget(null, 'See Also', 'panelRight');
			$objList = new atList(null, atList::TYPE_BULLET, 'menuRight');
			$objList->addControl(new atLink(null, 'Global Actions', $phpAnvil->site->webPath . 'phpAnvil/ViewGlobalActions', 'bulletAction'));
			$objList->addControl(new atLink(null, 'Build Config File', $phpAnvil->site->webPath . 'phpAnvil/BuildConfig', 'bulletExport'));
			$panel->addControl($objList);
//			$UI->rightColumn->addControl($panel);

			//---- Finalize and Display
			$UI->display();
		}

        return $return;
	}
}


//$controller = new ModulesController();
$phpAnvil->controller['phpAnvil.modules'] = new ModulesController();

?>
