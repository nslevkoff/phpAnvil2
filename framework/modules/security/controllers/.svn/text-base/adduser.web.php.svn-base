<?php

/**
* @file
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup       Add_User_Module phpAnvil_Controllers
*/

require_once(PHPANVIL_TOOLS_PATH . 'atForm.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atEntry.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atButton.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atContainer.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atLiteral.class.php');
require_once(PHPANVIL_TOOLS_PATH . 'atImage.class.php');


require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.web.php');

/**
* Web action to Add User section for an Security.
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Add_User_Module phpAnvil_Controllers
*/

class AddUserWebAction extends BaseWebAction {


    function loadModules() {
        global $phpAnvil;

//        $phpAnvil->loadWidget(MODULE_UI, 'header');
//        $phpAnvil->loadModule(MODULE_SECURITY);
//        $phpAnvil->loadWidget(MODULE_UI, 'menu');
    }


    function process() {
        global $phpAnvil, $modules;


        $this->enableTrace();

        if(isset($_POST['btn'])) {
            if ($_POST['btn'] == 'Save') {
                $newUser = new AdminUserModel($phpAnvil->db);
                $newUser->loadRequest();

//                $newTest->name = $_POST['name'];

//                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, "name = " . $newTest->name, self::TRACE_TYPE_DEBUG);

                $newUser->save();
            }

//            processFormButton($objAccount, 'Account Options', 'account_view.php');
        }

        /*$listHTML = '';

        $sql = 'SELECT * FROM admin_users';
        $objRS = $phpAnvil->db->execute($sql);

        while ($objRS->read()) {
            $listHTML .= $objRS->data('name') . '<br />';
        }
        $objRS->close(); */

        //----------------- Content -----------------
        $objContent = new atContainer('content');

//        $objContent->addControl(new HeaderWidget());
//        $objContent->addControl(new MenuWidget());

//        $objContent->addControl(new atLiteral(null, $listHTML));

        //----------------- Form Header
        $objForm = new atForm('forgot', 'post', '', null, false);
        $objForm->innerTemplate = 'adduser.tpl';
//        $objForm->bodyEnabled = false;
//        $objForm->footerEnabled = false;
//        $objContent->addControl($objForm);

        $objForm->addControl(new atEntry('name', 'name', 20, 50));
        $objForm->addControl(new atEntry('email', 'email', 20, 128));
        $objForm->addControl(new atEntry('username', 'username', 20, 50));
        $objForm->addControl(new atEntry('password', 'password', 20, 20));
    
        //----------------- Form Footer
//        $objForm = new atForm('accountinfo', 'post', '', null, false);
//        $objForm->headerEnabled = false;
//        $objForm->addControl(renderButtons($objAccount));


        $objForm->addControl(new atButton('save', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));

        $objForm->addControl(new atButton('cancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

        $objContent->addControl($objForm);

        $modules[MODULE_CONTENT]->page->addControl($objContent);
        $logo = new atImage('logo', $phpAnvil->site->webPath . 'themes/default/images/iSecurity.png');
        $logo->width = '48px';
        $logo->height = '48px';
        $modules[MODULE_CONTENT]->page->addControl($logo);
        $modules[MODULE_CONTENT]->page->addControl(new atLiteral('title', 'Add User'));
        $modules[MODULE_CONTENT]->page->innerTemplate = 'dialog.tpl';

        $modules[MODULE_CONTENT]->display();

    }
}


$objWebAction = new AddUserWebAction();

?>
