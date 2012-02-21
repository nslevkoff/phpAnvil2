<?php
/**
 * @file
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup       Dictionaries_Module phpAnvil_Controllers
 */

//require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilForm.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilHidden.class.php');
//require_once(PHPANVIL2_COMPONENT_PATH . 'anvilComboBox.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilEntry.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilButton.class.php');
require_once(PHPANVIL2_COMPONENT_PATH . 'anvilLiteral.class.php');


require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.controller.php');

/**
 * Web action to list dictionaries section for an i18n.
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dictionaries_Module phpAnvil_Controllers
 */
class DictionariesController extends BaseController
{

    public $dictionary;


    function __construct()
    {
        parent::__construct();

        $this->module       = 'i18n';
        $this->name         = 'Dictionaries Controller';
        $this->refName      = 'dictionaries';
        $this->version      = '1.0';
        $this->build        = '5';
        $this->copyright    =  '(c) 2011 Solutions By Design';

        $this->requiresAuthentication = true;


        return true;
    }


    function init()
    {
        global $phpAnvil;

        $return = parent::init();

        if ($return)
        {
            $phpAnvil->loadWidget('ui', 'ui');
            $phpAnvil->loadWidget('ui', 'grid');
            $phpAnvil->loadWidget('ui', 'tab_menu');


            if (isset($_POST['btn'])) {
                if ($_POST['btn'] == 'Save') {
                    if (isset($_POST['constant'])) {

                        $this->dictionary = new DictionaryModel($phpAnvil->db);
//                        $phpAnvil->module['i18n']->loadDictionary($_POST['idDictionaryID']);
//                        $modules[MODULE_I18N]->dictionary->loadRequest();
//
//                        $result = $modules[MODULE_I18N]->dictionary->save();
//                        $phpAnvil->actionMsg->add($modules[MODULE_I18N]->dictionary->constant . ' dictionary saved.');

//                        $renderPage = false;
                        header('Location: ' . $phpAnvil->site->webPath . 'i18n/Dictionaries');

                    } else {
                        $phpAnvil->errorMsg->add('Missing dictionary name.');
                    }
                }
            }
        }
    }


    function open()
    {
        global $phpAnvil;
//        global $firePHP;

        $return = parent::open();

        $this->enableTrace();

        if ($return)
        {
            //---- UI
            $UI = new UIWidget();
            $UI->menu->selected = MenuWidget::TAB_DEV;
            $UI->pageIcon = 'iDictionary.png';
            $UI->pageTitle = 'Dictionaries';
            $UI->preScript = 'js/i18n/dictionary.js';
            $UI->pageNavText = 'Developer Menu';
            $UI->pageNavPath = 'Dev/Menu';

            //----------------- Content -----------------
            //			$tabWidget = new TabMenuWidget();
            //			$tabWidget->url = 'Account/AccountTypes';
            //			$tabWidget->selected = 0;
            //			$tabWidget->addTab(0, 'All');
            //			$UI->content->addControl($tabWidget);


            $sql = 'SELECT * FROM ' . SQL_TABLE_I18N_DICTIONARIES;
            $sql .= ' WHERE record_status_id <> ' . RecordStatusModel::RECORD_STATUS_DELETED;
            //			if ($tabWidget->getFilter() != 'All') {
            //				$sql .= ' WHERE name REGEXP \'^' . $tabWidget->filter . '\'';
            //			}
            $sql .= ' ORDER BY constant';

            //            FB::log($sql);

            $objRS = $phpAnvil->db->execute($sql);

            $columns = $objRS->columns;

            if ($objRS->count() > 0) {
                while ($objRS->read()) {
                    $objGridWidget = new GridWidget();

                    if ($objRS->data('record_status_id') == RecordStatusModel::RECORD_STATUS_DISABLED) {
                        $objGridWidget->disabled = true;
                    }

                    $objGridWidget->icon = 'iDictionary_24.png';
                    //					$objGridWidget->linkURL = 'i18n/ViewDictionary?i=' . $objRS->data('dictionary_id');
                    $objGridWidget->onClick = 'javascript:editDictionary(' . $objRS->data('dictionary_id') . ')';
                    $objGridWidget->title = $objRS->data('constant');

                    $UI->content->addControl($objGridWidget);
                }
                $objRS->close();
            } else {
                $UI->content->addControl(new anvilLiteral(null, '<div class="devGrid">No dictionaries found.</div>'));
            }


            #================= RIGHT COLUMN ==================
            $newButton = new ActionButtonWidget('idNewDictionary', 'btnNew', 'New Dictionary', '#');
            $UI->actions->addControl($newButton);

            $newButton = new ActionButtonWidget('idBuildAllDictionaryFiles', 'btnExport', 'Build All Dictionary Files', '#');
            $UI->actions->addControl($newButton);

            //----- New/Edit Window
            $panel = new PanelWidget('idEntryPanel', 'New Dictionary', 'panelEdit');
            $objForm = new anvilForm('idEntryForm', 'post', '', null, false);
            $objForm->innerTemplate = 'i18n/editDictionaryPanel.tpl';

            $objForm->addControl(new anvilHidden('idDictionaryID', 'idDictionaryID', 0));
            $objForm->addControl(new anvilEntry('idConstant', 'constant', 20, 50));

            $newButton = new ActionButtonWidget('idSave', 'btnSave95', 'Save', '#');
            $newButton->class = 'actionFormButton';
            $objForm->addControl($newButton);

            $newButton = new ActionButtonWidget('idCancel', 'btnCancel95', 'Cancel', '#');
            $newButton->class = 'actionFormButton';
            $objForm->addControl($newButton);

            $panel->addControl($objForm);

            //--------- Other Actions ---------
            $otherActionsPanel = new PanelWidget('idOtherActionsPanel', 'Other Actions', 'panelRight');
            $objList = new anvilList(null, anvilList::TYPE_BULLET, 'menuRight');

            $objList->addControl(new anvilLink('idEnableDictionary', 'Enable Dictionary', '#', 'bulletEnable'));
            $objList->addControl(new anvilLink('idDisableDictionary', 'Disable Dictionary', '#', 'bulletDisable'));
            $objList->addControl(new anvilLink('idBuildDictionary', 'Build Dictionary File', '#', 'bulletExport'));

            $otherActionsPanel->addControl($objList);
            $panel->addControl($otherActionsPanel);

            $UI->actions->addControl($panel);


            //----------------- See Also -----------------
            $panel = new PanelWidget(null, 'See Also', 'panelRight');
            $objList = new anvilList(null, anvilList::TYPE_BULLET, 'menuRight');
            $objList->addControl(new anvilLink(null, 'Phrases', $phpAnvil->site->webPath . 'i18n/Phrases', 'bulletPhrase'));
            $panel->addControl($objList);
            $UI->rightColumn->addControl($panel);

            //---- Finalize and Display
            $UI->display();
        }
    }
}


//$objWebAction = new DictionariesWebAction();
$phpAnvil->controller['i18n.dictionaries'] = new DictionariesController();

?>
