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

        $phpAnvil->loadWidget(MODULE_UI, 'header');
//        $phpAnvil->loadWidget(MODULE_UI, 'menu');
    }


    function process() {
        global $phpAnvil, $modules;


        $this->enableTrace();

        if(isset($_POST['btn'])) {
            if ($_POST['btn'] == 'Save') {
                //$newTest = new TestModel($phpAnvil->db);
//                $newTest->loadRequest();

//                $newTest->name = $_POST['name'];

//                $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, "name = " . $newTest->name, self::TRACE_TYPE_DEBUG);

//                $newTest->save();
            }

//            processFormButton($objAccount, 'Account Options', 'account_view.php');
        }

        /*$listHTML = '';

        $sql = 'SELECT * FROM test';
        $objRS = $phpAnvil->db->execute($sql);

        while ($objRS->read()) {
            $listHTML .= $objRS->data('name') . '<br />';
        }
        $objRS->close();*/

        //----------------- Content -----------------
        $objContent = new atContainer('Content');

        $objContent->addControl(new HeaderWidget());
//        $objContent->addControl(new MenuWidget());

//        $objContent->addControl(new atLiteral(null, $listHTML));

        //----------------- Form Header
        $objForm = new atForm('forgot', 'post', '', null, false);
//        $objForm->innerTemplate = 'logon.tpl';
//        $objForm->bodyEnabled = false;
//        $objForm->footerEnabled = false;
//        $objContent->addControl($objForm);

        $objForm->addControl(new atEntry(null, 'forgot', 20, 20));
    
        //----------------- Form Footer
//        $objForm = new atForm('accountinfo', 'post', '', null, false);
//        $objForm->headerEnabled = false;
//        $objForm->addControl(renderButtons($objAccount));


        $objForm->addControl(new atButton('save', 'btn', atButton::TYPE_SUBMIT, 'Save', array('class' => 'button')));

        $objForm->addControl(new atButton('cancel', 'btn', atButton::TYPE_SUBMIT, 'Cancel', array('class' => 'button')));

        $objContent->addControl($objForm);

        $modules[MODULE_CONTENT]->page->addControl($objContent);


        $modules[MODULE_CONTENT]->display();

    }
}


$objWebAction = new AddUserWebAction();

?>