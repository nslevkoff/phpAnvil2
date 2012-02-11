<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       View_Users_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atHidden.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atPanel.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLink.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to view user for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        View_Users_Module phpAnvil_Controllers
*/

class AdminUsersWebAction extends BaseWebAction {


    function loadModules() {
        global $phpAnvil;

        $phpAnvil->loadWidget(MODULE_UI, 'admin_ui');
        $phpAnvil->loadWidget(MODULE_UI, 'admin_user');
    }


    function process() {
        global $phpAnvil, $modules;
        global $firePHP;

        $this->enableTrace();
        $i = isset($_GET['i']) ? $_GET['i'] : 0;
        $user = new AdminUserModel($phpAnvil->db, $i);
        $user->load();

        if(isset($_POST['btn'])) {
            if ($_POST['btn'] == 'Save') {
                if ($_POST['name'] != '') {
                    $user->loadRequest();

                    $result = $user->save();
                    FB::log($result->result);

                    $phpAnvil->actionMsg->add('Admin User Saved.');

                    echo $result->result;
                } else {
                    $phpAnvil->pageMsg->add('Missing User\'s Name.');
                    FB::log('Missing User\'s Name');
                    echo 'Missing User\'s Name';
                }

            }
        } else {
            //---- UI
            $UI = new AdminUIWidget();
            $UI->menu->selected = MenuWidget::TAB_OPTIONS;
            $UI->pageIcon = 'iAdminUser.png';
            $UI->pageTitle = $user->name;
/*            $UI->preScript = 'js/adminuser.js';*/

            $UI->pageAction->addControl(new atHidden('idUserID', 'id', '#', $user->id));
            $UI->pageAction->addControl(new atLink('idAddItem', '[edit]', '#', 'idAddItem'));

            //----------------- Content -----------------

            //----------------- Edit -----------------
            /*$objPanel = new atPanel('idEditHeader', 'idEditHeader');
            $UI->edit->addControl($objPanel);

            $objForm = new atForm('idForm', 'post', '', null, false);
            $objForm->footerEnabled = false;
            $objForm->addControl(new atHidden('idUserID', 'id', '0'));
            $UI->edit->addControl($objForm);

            $objForm = new atForm('idForm', 'post', '', null, false);
            $objForm->headerEnabled = false;
            $objForm->innerTemplate = 'adminuser.tpl';

            $objForm->addControl(new atEntry('idName', 'name', 20, 50));
            $objForm->addControl(new atEntry('idEmail', 'email', 30, 128));
            $objForm->addControl(new atEntry('idPhone', 'phone', 20, 20));
            $objForm->addControl(new atEntry('idUsername', 'username', 20, 50));
            $objForm->addControl(new atEntry('idPassword', 'password', 20, 50));*/

            /*$objTextBox = new atEntry('idPassword', 'password', 20, 50);
            $objTextBox->type = atEntry::TYPE_PASSWORD;
            $objForm->addControl($objTextBox);*/

            /*$objForm->addControl(new atButton('idSave', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));

            $objForm->addControl(new atButton('idCancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));
            $UI->edit->addControl($objForm);

            $UI->rightColumn->addControl(new atLink('idAddItem', '[add admin user]', '#', 'idAddItem'));*/
            //---- Finalize and Display
            $UI->display();
        }
    }
}


$objWebAction = new AdminUsersWebAction();

?>
