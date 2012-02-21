<?php
/**
*
* @file
* i18n Module Controller
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          i18n_Module
*
*/

require_once 'i18n.inc.php';

require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.module.php');

/**
*
* i18n Module Class
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup          i18n_Module
*
*/

class i18nModule extends BaseModule {

//	const NAME          = 'Internationalization Module';
//	const CODE          = 'i18n';
//	const VERSION       = '1.0';
//	const VERSION_BUILD = '16';
//	const VERSION_DTS   = '10/7/2010 4:30:00 PM PST';


	public $dictionary;
	public $language;
	public $phrase;
	public $translation;


	function __construct()
	{
//		global $phpAnvil;

//		$this->enableTrace();
		$return = parent::__construct();

        $this->type     = self::TYPE_CORE;
        $this->name     = 'i18n Module';
        $this->refName  = 'i18n';
        $this->version  = '1.0';
        $this->build    = '3';

//		$phpAnvil->loadDictionary('I18N');

		return $return;
	}


	function disableDictionary(Action $action)
	{
		global $phpAnvil;

		if ($this->loadDictionary($action->data['data'])) {
			$this->dictionary->disable();
			$this->dictionary->save();
			$phpAnvil->actionMsg->add($this->dictionary->constant . ' has been DISABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find dictionary! Disable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'i18n/Dictionaries';

		return $return;
	}


	function enableDictionary(Action $action)
	{
		global $phpAnvil;

		if ($this->loadDictionary($action->data['data'])) {
			$this->dictionary->enable();
			$this->dictionary->save();
			$phpAnvil->actionMsg->add($this->dictionary->constant . ' has been ENABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find dictionary! Enable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'i18n/Dictionaries';

		return $return;
	}


	function getDictionary(Action $action)
	{
		global $phpAnvil;
		global $firePHP;

		$return = 0;

		$sql = 'SELECT *';
		$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES;
		$sql .= ' WHERE dictionary_id = ' . $action->data['data'];
		$sql .= ' AND record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;

		$objRS = $phpAnvil->db->execute($sql);
		$return = $objRS->toArray();

		return $return;
	}


	function getDictionariesCombo(Action $action)
	{
		global $phpAnvil;
		global $firePHP;

		$return = 0;

		$sql = 'SELECT dictionary_id AS id, constant AS name';
		$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES;
		$sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
		$sql .= ' ORDER BY constant';

		$objRS = $phpAnvil->db->execute($sql);


		if ($action->source == SOURCE_AJAX && $action->data['responseActionID'] == anvilAjax::RESPONSE_ACTION_COMBOBOX) {
			$items = array(0, '-- Select Dictionary --');
			$return = $objRS->toArray($items);
		} else {
			$return = $objRS;
		}

		return $return;
	}


	function disablePhrase(Action $action)
	{
		global $phpAnvil;

		if ($this->loadPhrase($action->data['data'])) {
			$this->phrase->disable();
			$this->phrase->save();
			$phpAnvil->actionMsg->add($this->phrase->constant . ' has been DISABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find phrase! Disable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'i18n/Phrases';

		return $return;
	}


	function enablePhrase(Action $action)
	{
		global $phpAnvil;

		if ($this->loadPhrase($action->data['data'])) {
			$this->phrase->enable();
			$this->phrase->save();
			$phpAnvil->actionMsg->add($this->phrase->constant . ' has been ENABLED.');
		} else {
			$phpAnvil->errorMsg->add('Unable to find phrase! Enable canceled.');
		}

		$return = $phpAnvil->site->webPath . 'i18n/Phrases';

		return $return;
	}


	function loadDictionary($dictionaryID)
	{
		global $phpAnvil;

		$this->dictionary = new DictionaryModel($phpAnvil->db, $dictionaryID);
		return $this->dictionary->load();
	}


	function loadPhrase($phraseID)
	{
		global $phpAnvil;

		$this->phrase = new PhraseModel($phpAnvil->db, $phraseID);
		return $this->phrase->load();
	}


	public function createDictionaryFile($action) {
		global $phpAnvil;
		global $firePHP;

//		FB::log('$dictionaryID = ' . $dictionaryID);

		$return = true;

		$dictionaryID = $action->data['data'];

		$this->loadDictionary($dictionaryID);

		$sql = 'SELECT P.*';
		$sql .= ' FROM ' . SQL_TABLE_I18N_PHRASES . ' P';
		$sql .= ' JOIN ' . SQL_TABLE_I18N_DICTIONARY_PHRASES . ' DP';
		$sql .= ' ON DP.phrase_id=P.phrase_id';

		$sql .= ' WHERE DP.dictionary_id = ' . $dictionaryID;
		$sql .= ' ORDER BY P.constant';

//		FB::log($sql);

		$objRS = $phpAnvil->db->execute($sql);

		$content = '<?php' . "\n";
		$content2 = '';
		$content3 = '';
		$content4 = '';

		while ($objRS->read()) {
//			FB::log($this->dictionary);

			$phraseConstant = 'PHRASE_' . strtoupper($this->dictionary->constant . '_' . $objRS->data('constant'));

			$content .= "\tdefine('" . $phraseConstant . "', " . $objRS->data('phrase_id') . ");\n";

			$content2 .= "\t\$phrases[" . $phraseConstant . "] = '" . $objRS->data('phrase') . "';\n";

		}

		$content .= "\n" . $content2;

		$content .= '?>' . "\n";

		$filePath = LANG_PATH . 'en/' . $this->dictionary->constant . '.dictionary.php';

//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '$filePath = ' . $filePath, self::TRACE_TYPE_DEBUG);


		file_put_contents($filePath, $content);

		if ($action->source == SOURCE_TYPE_AJAX) {
			FB::log('Processing AJAX request...');

			$phpAnvil->actionMsg->add($this->dictionary->constant . ' dictionary file has been CREATED.');

			$return = $phpAnvil->site->webPath . 'i18n/Dictionaries';
		}

		FB::log($return);

		return $return;
	}


	function createAllDictionaryFiles(Action $action)
	{
		global $phpAnvil;
		global $firePHP;

		$return = 0;

		$sql = 'SELECT dictionary_id';
		$sql .= ' FROM ' . SQL_TABLE_I18N_DICTIONARIES;
		$sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
		$sql .= ' ORDER BY constant';

		$objRS = $phpAnvil->db->execute($sql);

		while ($objRS->read()) {
			$action->data['data'] = $objRS->data('dictionary_id');
			$return = $this->createDictionaryFile($action);
		}

		return $return;
	}



	function processAction(Action $action)
	{
		global $phpAnvil, $modules, $options;
		global $firePHP;

		$return = true;

		switch ($action->type) {
			case ACTION_CREATE_ALL_DICTIONARY_FILES:
				$return = $this->createAllDictionaryFiles($action);
				break;

			case ACTION_CREATE_DICTIONARY_FILE:
				$return = $this->createDictionaryFile($action);
				break;

			CASE ACTION_DISABLE_DICTIONARY:
				$return = $this->disableDictionary($action);
				break;

			CASE ACTION_DISABLE_PHRASE:
				$return = $this->disablePhrase($action);
				break;

			CASE ACTION_ENABLE_DICTIONARY:
				$return = $this->enableDictionary($action);
				break;

			CASE ACTION_ENABLE_PHRASE:
				$return = $this->enablePhrase($action);
				break;

			CASE ACTION_GET_DICTIONARY:
				$return = $this->getDictionary($action);
				break;

			CASE ACTION_GET_DICTIONARIES_COMBO:
				$return = $this->getDictionariesCombo($action);
				break;

//            CASE ACTION_BUILD_DOXYGEN_FILES:
            CASE $phpAnvil->getActionID('doxygen', 'BUILD_DOXYGEN_FILES'):

//                $modules[MODULE_DOXYGEN]->copyright = $options['app']['copyright'];
//                $modules[MODULE_DOXYGEN]->inGroups = 'Account_Module Pulse_DB_Tables';
//                $modules[MODULE_DOXYGEN]->see[] = 'Account_Module';

//                $modules[MODULE_DOXYGEN]->page = 'accounts_table accounts Table';

                $modules[MODULE_DOXYGEN]->filePath = PHPANVIL_MODULES_PATH .
                    self::CODE . '/doxygen/';


                //---- dictionary_phrases
                $dot = '';
                $dot .= 'digraph dictionary_phrases {' . "\n";
                $dot .= '    rankdir=LR;' . "\n";
                $dot .= '    node [shape=record, fillcolor="#F2F2F2", fontname=Helvetica, fontsize=9];' . "\n";
                $dot .= '    edge [fontname=Helvetica, fontsize=9, style=solid];' . "\n";
                $dot .= '    a [label="sbd_i18n_dictionaries", URL="@ref sbd_i18n_dictionaries"];' . "\n";
                $dot .= '    b [label="sbd_i18n_dictionary_phrases", style=filled];' . "\n";
                $dot .= '    c [label="sbd_i18n_languages", URL="@ref sbd_i18n_languages"];' . "\n";
                $dot .= '    d [label="sbd_i18n_phrases", URL="@ref sbd_i18n_phrases"];' . "\n";
                $dot .= '    e [label="sbd_i18n_translations", URL="@ref sbd_i18n_translations"];' . "\n";
                $dot .= '    b -> a [arrowhead=normal, arrowtail=crow, dir=both, label="dictionary_id"];' . "\n";
                $dot .= '    b -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> c [arrowhead=normal, arrowtail=crow, dir=both, label="language_id"];' . "\n";
                $dot .= '}' . "\n";

                $modules[MODULE_DOXYGEN]->dotDiagram = $dot;

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_I18N_DICTIONARY_PHRASES);


                //---- dictionaries
                $dot = '';
                $dot .= 'digraph dictionaries {' . "\n";
                $dot .= '    rankdir=LR;' . "\n";
                $dot .= '    node [shape=record, fillcolor="#F2F2F2", fontname=Helvetica, fontsize=9];' . "\n";
                $dot .= '    edge [fontname=Helvetica, fontsize=9, style=solid];' . "\n";
                $dot .= '    a [label="sbd_i18n_dictionaries", style=filled];' . "\n";
                $dot .= '    b [label="sbd_i18n_dictionary_phrases", URL="@ref sbd_i18n_dictionary_phrases"];' . "\n";
                $dot .= '    c [label="sbd_i18n_languages", URL="@ref sbd_i18n_languages"];' . "\n";
                $dot .= '    d [label="sbd_i18n_phrases", URL="@ref sbd_i18n_phrases"];' . "\n";
                $dot .= '    e [label="sbd_i18n_translations", URL="@ref sbd_i18n_translations"];' . "\n";
                $dot .= '    b -> a [arrowhead=normal, arrowtail=crow, dir=both, label="dictionary_id"];' . "\n";
                $dot .= '    b -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> c [arrowhead=normal, arrowtail=crow, dir=both, label="language_id"];' . "\n";
                $dot .= '}' . "\n";

                $modules[MODULE_DOXYGEN]->dotDiagram = $dot;
                $modules[MODULE_DOXYGEN]->includeData = TRUE;

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_I18N_DICTIONARIES);

                $modules[MODULE_DOXYGEN]->dotDiagram = '';


                //---- languages
                $dot = '';
                $dot .= 'digraph languages {' . "\n";
                $dot .= '    rankdir=LR;' . "\n";
                $dot .= '    node [shape=record, fillcolor="#F2F2F2", fontname=Helvetica, fontsize=9];' . "\n";
                $dot .= '    edge [fontname=Helvetica, fontsize=9, style=solid];' . "\n";
                $dot .= '    a [label="sbd_i18n_dictionaries", URL="@ref sbd_i18n_dictionaries"];' . "\n";
                $dot .= '    b [label="sbd_i18n_dictionary_phrases", URL="@ref sbd_i18n_dictionary_phrases"];' . "\n";
                $dot .= '    c [label="sbd_i18n_languages", style=filled];' . "\n";
                $dot .= '    d [label="sbd_i18n_phrases", URL="@ref sbd_i18n_phrases"];' . "\n";
                $dot .= '    e [label="sbd_i18n_translations", URL="@ref sbd_i18n_translations"];' . "\n";
                $dot .= '    b -> a [arrowhead=normal, arrowtail=crow, dir=both, label="dictionary_id"];' . "\n";
                $dot .= '    b -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> c [arrowhead=normal, arrowtail=crow, dir=both, label="language_id"];' . "\n";
                $dot .= '}' . "\n";

                $modules[MODULE_DOXYGEN]->dotDiagram = $dot;

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_I18N_LANGUAGES);

                $modules[MODULE_DOXYGEN]->includeData = FALSE;


                //---- phrases
                $dot = '';
                $dot .= 'digraph phrases {' . "\n";
                $dot .= '    rankdir=LR;' . "\n";
                $dot .= '    node [shape=record, fillcolor="#F2F2F2", fontname=Helvetica, fontsize=9];' . "\n";
                $dot .= '    edge [fontname=Helvetica, fontsize=9, style=solid];' . "\n";
                $dot .= '    a [label="sbd_i18n_dictionaries", URL="@ref sbd_i18n_dictionaries"];' . "\n";
                $dot .= '    b [label="sbd_i18n_dictionary_phrases", URL="@ref sbd_i18n_dictionary_phrases"];' . "\n";
                $dot .= '    c [label="sbd_i18n_languages", URL="@ref sbd_i18n_languages"];' . "\n";
                $dot .= '    d [label="sbd_i18n_phrases", style=filled];' . "\n";
                $dot .= '    e [label="sbd_i18n_translations", URL="@ref sbd_i18n_translations"];' . "\n";
                $dot .= '    b -> a [arrowhead=normal, arrowtail=crow, dir=both, label="dictionary_id"];' . "\n";
                $dot .= '    b -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> c [arrowhead=normal, arrowtail=crow, dir=both, label="language_id"];' . "\n";
                $dot .= '}' . "\n";

                $modules[MODULE_DOXYGEN]->dotDiagram = $dot;

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_I18N_PHRASES);


                //---- translations
                $dot = '';
                $dot .= 'digraph translations {' . "\n";
                $dot .= '    rankdir=LR;' . "\n";
                $dot .= '    node [shape=record, fillcolor="#F2F2F2", fontname=Helvetica, fontsize=9];' . "\n";
                $dot .= '    edge [fontname=Helvetica, fontsize=9, style=solid];' . "\n";
                $dot .= '    a [label="sbd_i18n_dictionaries", URL="@ref sbd_i18n_dictionaries"];' . "\n";
                $dot .= '    b [label="sbd_i18n_dictionary_phrases", URL="@ref sbd_i18n_dictionary_phrases"];' . "\n";
                $dot .= '    c [label="sbd_i18n_languages", URL="@ref sbd_i18n_languages"];' . "\n";
                $dot .= '    d [label="sbd_i18n_phrases", URL="@ref sbd_i18n_phrases"];' . "\n";
                $dot .= '    e [label="sbd_i18n_translations", style=filled];' . "\n";
                $dot .= '    b -> a [arrowhead=normal, arrowtail=crow, dir=both, label="dictionary_id"];' . "\n";
                $dot .= '    b -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> d [arrowhead=normal, arrowtail=crow, dir=both, label="phrase_id"];' . "\n";
                $dot .= '    e -> c [arrowhead=normal, arrowtail=crow, dir=both, label="language_id"];' . "\n";
                $dot .= '}' . "\n";

                $modules[MODULE_DOXYGEN]->dotDiagram = $dot;

                $phpAnvil->processNewAction($action->source, MODULE_DOXYGEN,
                    ACTION_CREATE_DB_TABLE_DOXYGEN_FILE,
                    SQL_TABLE_I18N_TRANSLATIONS);


                break;

		}

		return $return;
	}

}

$phpAnvil->module['i18n'] = new i18nModule();

?>
