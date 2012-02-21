<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Dictionary_Module phpAnvil_Controllers
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
* Web action to list dictionary section for an i18n.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Dictionary_Module phpAnvil_Controllers
*/
class PhraseWebAction extends BaseWebAction {


	function loadModules() {
		global $phpAnvil;

//		$phpAnvil->loadModule(MODULE_MARKETING);
		$phpAnvil->loadWidget(MODULE_UI, 'ui');
//        $phpAnvil->loadWidget(MODULE_UI, 'product_tab');
	}


	function process() {
		global $phpAnvil, $modules;
		global $firePHP;


		$renderPage = true;
		$this->enableTrace();
		$i = isset($_GET['i']) ? $_GET['i'] : 0;
		$i = isset($_POST['id']) ? $_POST['id'] : $i;
		$modules[MODULE_I18N]->loadPhrase($i);

		$backURL = 'i18n/Phrases';
		$returnURL =  $backURL;

//		FB::log($_POST);

		if(isset($_POST['btn'])) {

//			FB::log('Form submitted...');
//			FB::log($_POST['btn']);

			if ($_POST['btn'] == 'Save') {
				$modules[MODULE_I18N]->phrase->loadRequest();
				$modules[MODULE_I18N]->phrase->save();

				//---- Clear All Previously Selected Dictionaries for the Phrase
//				FB::log('Clearing All Previously Selected Dictionaries for the Phrase...');

				$sql = 'DELETE FROM ' . SQL_TABLE_I18N_DICTIONARY_PHRASES;
				$sql .= ' WHERE phrase_id = ' . $modules[MODULE_I18N]->phrase->id;

				$phpAnvil->db->execute($sql);

//				FB::log($sql);

//				FB::log($_POST);

				//---- Save Selected Dictionaries
				if (isset($_POST['dID'])) {
//					FB::log('dID Detected');

					$dictionaryIDArray = $_POST['dID'];
					$newDictionaryPhrase = new DictionaryPhraseModel($phpAnvil->db);
					$newDictionaryPhrase->phraseID = $modules[MODULE_I18N]->phrase->id;

					foreach ($dictionaryIDArray as $dictionaryID) {
//						FB::log('Saving Dictionary ID #' . $dictionaryID);

						$newDictionaryPhrase->id = 0;
						$newDictionaryPhrase->dictionaryID = $dictionaryID;
						$newDictionaryPhrase->save();
					}

				}

				$phpAnvil->actionMsg->add($modules[MODULE_I18N]->phrase->name . ' saved.');

				$renderPage = false;
				header('Location: ' . $phpAnvil->site->webPath . $returnURL);

			} else {
				$renderPage = false;
				header('Location: ' . $phpAnvil->site->webPath . $backURL);
			}
		}


		if ($renderPage) {

			//---- UI
			$UI = new UIWidget();
			$UI->menu->selected = MenuWidget::TAB_DEV;
			$UI->preScript = 'js/i18n/edit_phrase.js';

			$UI->pageNavText = 'All Phrases';
			$UI->pageNavPath = $backURL;

			$UI->pageIcon = 'iList.png';

			if ($modules[MODULE_I18N]->phrase->isNew()) {
				$UI->pageTitle = 'New Phrase';
			} else {
				$UI->pageTitle = $modules[MODULE_I18N]->phrase->constant;
				$UI->pageTitleRight = 'Edit Phrase';
			}


			if ($modules[MODULE_I18N]->phrase->isDisabled()) {
//				$UI->pageIcon = 'iSite_g.png';
				$UI->disabled = true;
				$phpAnvil->pageMsg->add('This phrase is currently disabled.');
			}


			//----------------- Content -----------------

			$objForm = new anvilForm('idEntryForm', 'post', '', null, false);
			$objForm->innerTemplate = 'i18n/editPhrase.tpl';


			$objForm->addControl(new anvilHidden('idPhraseID', 'id', $modules[MODULE_I18N]->phrase->id));
//			$objForm->addControl(new anvilEntry('idName', 'name', 50, 80, $modules[MODULE_I18N]->phrase->name));
			$objForm->addControl(new anvilEntry('idConstant', 'constant', 50, 50, $modules[MODULE_I18N]->phrase->constant));
			$objForm->addControl(new anvilMemo('idPhrase', 'phrase', 50, 4, $modules[MODULE_I18N]->phrase->phrase));

			$dictionariesPanel = new anvilPanel('idDictionariesPanel');

			$sql = 'SELECT D.*, DP.dictionary_phrase_id';
			$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES . ' D';
			$sql .= ' LEFT JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
			$sql .= ' ON DP.dictionary_id=D.dictionary_id';
			$sql .= ' WHERE D.record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
			$sql .= ' AND (DP.phrase_id=' . $i . ' OR DP.phrase_id IS NULL)';

//			if ($tabWidget->getFilter() != 'All') {
//				$sql .= ' AND name REGEXP \'^' . $tabWidget->filter . '\'';
//			}
			$sql .= ' ORDER BY name';

			FB::log($sql);

			$objRS = $phpAnvil->db->execute($sql);

			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$newCheckBox = new anvilCheckBox('', 'dID[]', $objRS->data('dictionary_id'), $objRS->data('name') . '&nbsp;&nbsp;');
					if ($objRS->data('dictionary_phrase_id') > 0) {
						$newCheckBox->checked = true;
					}
					$dictionariesPanel->addControl($newCheckBox);
				}
			}

			$objForm->addControl($dictionariesPanel);


//			$objForm->addControl(new anvilButton('save', 'btn', anvilButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));
//			$objForm->addControl(new anvilButton('cancel', 'btn', anvilButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

			$newButton = new ActionButtonWidget('idSave', 'btnSave', 'Save', '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idEntryForm\').submit();';
			$objForm->addControl($newButton);

			$newButton = new ActionButtonWidget('Cancel', 'btnCancel', 'Cancel', $phpAnvil->site->webPath . $backURL);
			$newButton->class = 'actionFormButton';
			$objForm->addControl($newButton);

			$UI->content->addControl($objForm);

			#========== RIGHT BAR ==========================

			if (!$modules[MODULE_I18N]->phrase->isNew()) {

				if ($modules[MODULE_I18N]->phrase->isDisabled()) {
	//				$newButton = new ActionButtonWidget('', 'btnActivate', '#activate');
					$UI->actions->addControl(new ActionButtonWidget('idEnablePhrase', 'btnEnable', 'Enable Phrase', '#'));
				} else {
	//				$newButton = new ActionButtonWidget('', 'btnDisable', '#disable');
					$UI->actions->addControl(new ActionButtonWidget('idDisablePhrase', 'btnDisable', 'Disable Phrase', '#'));
				}

				$UI->actions->addControl(new ActionButtonWidget('idDeletePhrase', 'btnDelete', 'Delete Phrase', '#'));
			}
			$UI->display();
		}
	}
}


$objWebAction = new PhraseWebAction();

?>