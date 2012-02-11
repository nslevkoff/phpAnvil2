<?php
/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Phrase_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atMemo.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atCheckBox.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atComboBox.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atContainer.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atImage.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to load phrase section for an i18n.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Phrase_Module phpAnvil_Controllers
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

				$phpAnvil->actionMsg->add($modules[MODULE_I18N]->phrase->constant . ' saved.');

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

			$UI->pageIcon = 'iPhrase3.png';

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

			$objForm = new atForm('idEntryForm', 'post', '', null, false);
			$objForm->innerTemplate = 'i18n/editPhrase.tpl';


			$objForm->addControl(new atHidden('idPhraseID', 'id', $modules[MODULE_I18N]->phrase->id));
//			$objForm->addControl(new atEntry('idName', 'name', 50, 80, $modules[MODULE_I18N]->phrase->name));
			$objForm->addControl(new atEntry('idConstant', 'constant', 50, 50, $modules[MODULE_I18N]->phrase->constant));
			$objForm->addControl(new atMemo('idPhrase', 'phrase', 50, 4, $modules[MODULE_I18N]->phrase->phrase));

			$dictionariesPanel = new atPanel('idDictionariesPanel');

//SELECT D.dictionary_id, D.constant, D.name, COUNT(DP.dictionary_phrase_id) FROM sbd_i18n_dictionaries D LEFT JOIN sbd_i18n_dictionary_phrases DP ON DP.dictionary_id=D.dictionary_id
//WHERE D.record_status_id <> 30
//GROUP BY D.dictionary_id, D.constant, D.name
//ORDER BY D.constant

			$sql = 'SELECT D.dictionary_id, D.constant, COUNT(DP.dictionary_phrase_id) AS phrases';
			$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES . ' D';
			$sql .= ' LEFT JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
			$sql .= ' ON (DP.dictionary_id=D.dictionary_id';
			if (!$modules[MODULE_I18N]->phrase->isNew()) {
				$sql .= ' AND DP.phrase_id=' . $i;
			}
			$sql .= ')';
//			$sql .= ' WHERE D.record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
            $sql .= ' WHERE D.record_status_id = ' . RecordStatusModel::RECORD_STATUS_ACTIVE;

			$sql .= ' GROUP BY D.dictionary_id, D.constant';

//			if ($tabWidget->getFilter() != 'All') {
//				$sql .= ' AND name REGEXP \'^' . $tabWidget->filter . '\'';
//			}
			$sql .= ' ORDER BY D.constant';

//			FB::log($sql);

			$objRS = $phpAnvil->db->execute($sql);

			if ($objRS->count() > 0) {
				while ($objRS->read()) {
					$newCheckBox = new atCheckBox('', 'dID[]', $objRS->data('dictionary_id'), $objRS->data('constant') . '&nbsp;&nbsp;');
//					if ($objRS->data('dictionary_phrase_id') > 0) {
					if (!$modules[MODULE_I18N]->phrase->isNew() && $objRS->data('phrases') > 0) {
						$newCheckBox->checked = true;
					}
					$dictionariesPanel->addControl($newCheckBox);
				}
			}

			$objForm->addControl($dictionariesPanel);


//			$objForm->addControl(new atButton('save', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));
//			$objForm->addControl(new atButton('cancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

			$newButton = new ActionButtonWidget('idSave', 'btnSave95', 'Save', '#');
			$newButton->class = 'actionFormButton';
			$newButton->onclick = '$(\'#idEntryForm\').submit();';
			$objForm->addControl($newButton);

			$newButton = new ActionButtonWidget('Cancel', 'btnCancel95', 'Cancel', $phpAnvil->site->webPath . $backURL);
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
