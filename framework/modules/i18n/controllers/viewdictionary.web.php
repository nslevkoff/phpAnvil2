<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       View_Dictionary_Module phpAnvil_Controllers
*/

require_once(PHPANVIL2_COMPONENT_PATH . 'anvilForm.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilEntry.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilMemo.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilCheckBox.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilComboBox.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilButton.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilImage.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilHidden.class.php');


require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to view dictionary for an i18n.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        View_Dictionary_Module phpAnvil_Controllers
*/
class PhraseWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadWidget(MODULE_UI, 'ui');
		$phpAnvil->loadWidget(MODULE_UI, 'panel');
		$phpAnvil->loadWidget(MODULE_UI, 'grid');
	}


	function process() {
		global $phpAnvil, $modules;
		global $firePHP;


		$renderPage = true;
		$this->enableTrace();

		$buildFile = isset($_GET['b']) ? $_GET['b'] : 0;

		$i = isset($_GET['i']) ? $_GET['i'] : 0;
//		$i = isset($_POST['id']) ? $_POST['id'] : $i;
		$modules[MODULE_I18N]->loadDictionary($i);

		$backURL = 'i18n/Dictionaries';
		$returnURL =  $backURL;

//		FB::log($_POST);

		if ($buildFile) {
			$phpAnvil->processNewAction(SOURCE_USER, MODULE_I18N, ACTION_CREATE_DICTIONARY_FILE, $modules[MODULE_I18N]->dictionary->id);

			$phpAnvil->actionMsg->add($modules[MODULE_I18N]->dictionary->constant . ' dictionary file created.');
		}


		if ($renderPage) {

			//---- UI
			$UI = new UIWidget();
			$UI->menu->selected = MenuWidget::TAB_DEV;
			$UI->preScript = 'js/i18n/view_dictionary.js';

			$UI->pageNavText = 'All Dictionaries';
			$UI->pageNavPath = $backURL;

			$UI->pageIcon = 'iList.png';

			$UI->pageTitle = $modules[MODULE_I18N]->dictionary->name;
			$UI->pageTitleRight = 'View Dictionary';


			if ($modules[MODULE_I18N]->dictionary->isDisabled()) {
//				$UI->pageIcon = 'iSite_g.png';
				$UI->disabled = true;
				$phpAnvil->pageMsg->add('This dictionary is currently disabled.');
			}


			//----------------- Content -----------------

			$panel = new PanelWidget(null, 'Dictionary Phrases');
			$UI->content->addControl($panel);

			$sql = 'SELECT P.*';
			$sql .= ' FROM ' . SQL_TABLE_I18N_PHRASES . ' P';
			$sql .= ' JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
			$sql .= ' ON P.phrase_id=DP.phrase_id';
			$sql .= ' WHERE P.record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
			$sql .= ' AND DP.dictionary_id=' . $i;


//			$sql = 'SELECT * FROM ' . SQL_TABLE_I18N_PHRASES;
//			$sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
//			if ($tabWidget->getFilter() != 'All') {
//				$sql .= ' WHERE name REGEXP \'^' . $tabWidget->filter . '\'';
//			}
			$sql .= ' ORDER BY P.constant';

//            FB::log($sql);

			$objRS = $phpAnvil->db->execute($sql);

			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$objGridWidget = new GridWidget();

					if ($objRS->data('record_status_id') == RecordStatusModel::RECORD_STATUS_DISABLED) {
						$objGridWidget->disabled = true;
					}

					$objGridWidget->icon = 'iList_24.png';
					$objGridWidget->linkURL = 'i18n/Phrase?i=' . $objRS->data('phrase_id');
//					$objGridWidget->title = $objRS->data('name');
					$objGridWidget->title = $objRS->data('constant');

					$content = $objRS->data('phrase');

					if (!empty($content)) {
						$objGridWidget->addControl(new anvilLiteral('', $content));
					}

					$UI->content->addControl($objGridWidget);
				}
				$objRS->close();
			} else {
				$UI->content->addControl(new anvilLiteral(null, '<div class="devGrid">No phrases found.</div>'));
			}


			#========== RIGHT BAR ==========================


				if ($modules[MODULE_I18N]->dictionary->isDisabled()) {
	//				$newButton = new ActionButtonWidget('', 'btnActivate', '#activate');
					$UI->actions->addControl(new ActionButtonWidget('idEnableDictionary', 'btnEnable', 'Enable Dictionary', '#'));
				} else {
	//				$newButton = new ActionButtonWidget('', 'btnDisable', '#disable');
					$UI->actions->addControl(new ActionButtonWidget('idDisableDictionary', 'btnDisable', 'Disable Dictionary', '#'));
				}

//				$UI->actions->addControl(new ActionButtonWidget('idDeletePhrase', 'btnDelete', 'Delete Phrase', '#'));

			$newButton = new ActionButtonWidget('idBuildDictionary', 'btnExport', 'Build Dictionary File',
												$phpAnvil->site->webPath . 'i18n/ViewDictionary?b=1&i=' . $modules[MODULE_I18N]->dictionary->id);

			$UI->actions->addControl($newButton);

			$UI->display();
		}
	}
}


$objWebAction = new PhraseWebAction();

?>