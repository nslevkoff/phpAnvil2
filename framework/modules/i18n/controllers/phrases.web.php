<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Phrases_Module phpAnvil_Controllers
*/


//require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilForm.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilHidden.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilComboBox.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilEntry.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilButton.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');


require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to load phrases section for an i18n.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Phrases_Module phpAnvil_Controllers
*/
class DictionariesWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

		$phpAnvil->loadDictionary('DEVELOPER');

		$phpAnvil->loadWidget(MODULE_UI, 'ui');
		$phpAnvil->loadWidget(MODULE_UI, 'grid');
		$phpAnvil->loadWidget(MODULE_UI, 'tab_menu');
	}


	function process() {
		global $phpAnvil, $modules, $phrases;
		global $firePHP;

		$this->enableTrace();

		$renderPage = true;

		$dictionaryID = 0;

		if (isset($_SESSION['dictionaryID'])) {
			$dictionaryID = $_SESSION['dictionaryID'];
		}

		if(isset($_POST['btn'])) {
			if ($_POST['btn'] == 'Filter') {
				$dictionaryID = $_POST['dID'];

				$_SESSION['dictionaryID'] = $dictionaryID;
			}
		}

		$modules[MODULE_I18N]->loadDictionary($dictionaryID);

		if ($renderPage) {
			//---- UI
			$UI = new UIWidget();
			$UI->menu->selected = MenuWidget::TAB_DEV;
			$UI->pageIcon = 'iPhrase3.png';

			FB::log($phrases);

			if (!$modules[MODULE_I18N]->dictionary->isNew()) {
				$UI->pageTitle = $modules[MODULE_I18N]->dictionary->constant;
				$UI->pageTitleRight = $phrases[PHRASE_I18N_PHRASES];
			} else {
				$UI->pageTitle = $phrases[PHRASE_I18N_PHRASES];
			}
			$UI->preScript = 'js/i18n/phrases.js';
			$UI->pageNavText = $phrases[PHRASE_DEVELOPER_DEVELOPER_MENU];
			$UI->pageNavPath = 'Dev/Menu';

			//----------------- Content -----------------
			$tabWidget = new TabMenuWidget();
			$tabWidget->url = 'i18n/Phrases';
			$tabWidget->selected = 0;
//			$tabWidget->addTab(0, '-');
			$tabWidget->addTab(0, '*');
			$UI->content->addControl($tabWidget);


//			if ($tabWidget->getFilter() != '-') {

			if (!$modules[MODULE_I18N]->dictionary->isNew()) {
				$sql = 'SELECT P.*';
				$sql .= ' FROM ' . SQL_TABLE_I18N_PHRASES . ' P';
				$sql .= ' JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
				$sql .= ' ON P.phrase_id=DP.phrase_id';
				$sql .= ' WHERE P.record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
				$sql .= ' AND DP.dictionary_id=' . $modules[MODULE_I18N]->dictionary->id;
			} else {
				$sql = 'SELECT * FROM ' . SQL_TABLE_I18N_PHRASES;
				$sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
			}
			if ($tabWidget->getFilter() != '*') {
				$sql .= ' AND constant REGEXP \'^' . $tabWidget->filter . '\'';
			}
			$sql .= ' ORDER BY constant';

	//            FB::log($sql);

				$objRS = $phpAnvil->db->execute($sql);

				if ($objRS->count() > 0) {
					while ($objRS->read()) {
						$objGridWidget = new GridWidget();

						if ($objRS->data('record_status_id') == RecordStatusModel::RECORD_STATUS_DISABLED) {
							$objGridWidget->disabled = true;
						}

						$objGridWidget->icon = 'iPhrase3_24.png';
						$objGridWidget->linkURL = $phpAnvil->site->webPath . 'i18n/Phrase?i=' . $objRS->data('phrase_id');
	//					$objGridWidget->title = $objRS->data('name');
						$objGridWidget->title = $objRS->data('constant');

						$content = $objRS->data('phrase');

						//---- Get Dictionaries
						$sql = 'SELECT D.dictionary_id, D.constant';
						$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES . ' D';
						$sql .= ' JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
						$sql .= ' ON D.dictionary_id=DP.dictionary_id';
						$sql .= ' WHERE D.record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
						$sql .= ' AND DP.phrase_id=' . $objRS->data('phrase_id');

						$objDictionaries = $phpAnvil->db->execute($sql);
						if ($objDictionaries->count() > 0) {
							$content .= '<div class="extra">';

							while ($objDictionaries->read()) {
								$content .= '<span class="bulletDictionary">' . $objDictionaries->data('constant') . '</span>';
							}
							$content .= '</div>';
						}

						if (!empty($content)) {
							$objGridWidget->addControl(new anvilLiteral('', $content));
						}

						$UI->content->addControl($objGridWidget);
					}
					$objRS->close();
				} else {
					$UI->content->addControl(new anvilLiteral(null, '<div class="anvilGrid">No phrases found.</div>'));
				}
//			}

			#================= RIGHT COLUMN ==================
			$newButton = new ActionButtonWidget('idFilterList', 'btnRefresh', $phrases[PHRASE_GLOBAL_FILTER_LIST], '#');
			$UI->actions->addControl($newButton);

			$newButton = new ActionButtonWidget('idNewPhrase', 'btnNew', $phrases[PHRASE_I18N_NEW_PHRASE], $phpAnvil->site->webPath . 'i18n/Phrase');
			$UI->actions->addControl($newButton);

			//----- Filter Window
			$panel = new PanelWidget('idFilterPanel', $phrases[PHRASE_GLOBAL_FILTER_LIST], 'panelEdit');
			$objForm = new anvilForm('idFilterForm', 'post', '', null, false);
			$objForm->innerTemplate = 'i18n/filterPhrasesPanel.tpl';

//			$objForm->addControl(new anvilHidden('idDictionaryID', 'idDictionaryID', 0));

			$sql = 'SELECT dictionary_id AS id, constant AS name FROM ' . SQL_TABLE_I18N_DICTIONARIES;
            $sql .= ' WHERE record_status_id = ' . RecordStatusModel::RECORD_STATUS_ACTIVE;
			$sql .= ' ORDER BY constant';
			$objRS = $phpAnvil->db->execute($sql);

			$objComboBox = new anvilComboBox('idDictionaryCombo', 'dID');
			$objComboBox->addPreItem(0, '** ALL Dictionaries **');
			$objComboBox->recordset = $objRS;
			$objForm->addControl($objComboBox);


			$newButton = new ActionButtonWidget('idFilter', 'btnSave95', 'Filter', '#');
			$newButton->class = 'actionFormButton';
			$objForm->addControl($newButton);

			$newButton = new ActionButtonWidget('idCancel', 'btnCancel95', 'Cancel', '#');
			$newButton->class = 'actionFormButton';
			$objForm->addControl($newButton);

			$panel->addControl($objForm);
			$UI->actions->addControl($panel);


			//----------------- See Also -----------------
			$panel = new PanelWidget(null, 'See Also', 'panelRight');
			$objList = new anvilList(null, anvilList::TYPE_BULLET, 'menuRight');
			$objList->addControl(new anvilLink(null, 'Dictionaries', $phpAnvil->site->webPath . 'i18n/Dictionaries', 'bulletDictionary'));
			$panel->addControl($objList);
			$UI->rightColumn->addControl($panel);


			//---- Finalize and Display
			$UI->display();
		}
	}
}


$objWebAction = new DictionariesWebAction();

?>
