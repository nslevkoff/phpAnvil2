<?php
/**
 * @file
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup       Dev_Module Pulse_Controllers
 */
require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.controller.php');

/**
 * Web action to load admin users section for an account.
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dev_Module Pulse_Controllers
 */

class DashboardController extends BaseController
{


    function init()
    {
        global $phpAnvil;

        $return = parent::init();

        if ($return)
        {
        $phpAnvil->loadWidget('ANDI', 'ui');
        $phpAnvil->loadWidget('ANDI', 'menu');
//        $phpAnvil->loadWidget(MODULE_SBD, 'ui');
//        $phpAnvil->loadWidget(MODULE_UI, 'image_menu');
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;


        $this->enableTrace();

        $return = parent::open();

		$this->enableTrace();

		if ($return)
        {
			//---- UI
			$UI = new UIWidget();
//			$UI->menu->selected = MenuWidget::TAB_DEV;
			$UI->pageTitle = 'Development Dashboard';


        #---- Header
//        $UI = new SBDUIWidget();
//        $UI->menu->selected = MenuWidget::TAB_DEV;
//        $UI->pageIcon = 'iConstruction.png';
//        $UI->pageTitle = 'Developer Menu';
//
//        #----------------- Content -----------------
//
//
//        //		$UI->content->addControl(new atLiteral('test', 'test'));
//
//        //		$UI->content->addControl(new ImageMenuWidget("iAdminUser.png", "Admin Users", "Maintain users that have access to the admin interface."));
//
//        $newWidget = new ImageMenuWidget();
//        $newWidget->icon = "iDictionary.png";
//        $newWidget->title = "Dictionaries";
//        $newWidget->linkURL = 'i18n/Dictionaries';
//        $newWidget->description = "";
//        $UI->content->addControl($newWidget);
//
//        $newWidget = new ImageMenuWidget();
//        $newWidget->icon = "iList.png";
//        $newWidget->title = "Languages";
//        $newWidget->linkURL = 'i18n/Languages';
//        $newWidget->description = "";
//        $UI->content->addControl($newWidget);
//
//
//        $newWidget = new ImageMenuWidget();
//        $newWidget->icon = "iModule.png";
//        $newWidget->title = "Modules";
//        $newWidget->linkURL = 'phpAnvil/Modules';
//        $newWidget->description = "View and install application modules.";
//        $UI->content->addControl($newWidget);
//
//        $newWidget = new ImageMenuWidget();
//        $newWidget->icon = "iPhrase3.png";
//        $newWidget->title = "Phrases";
//        $newWidget->linkURL = 'i18n/Phrases';
//        $newWidget->description = "";
//        $UI->content->addControl($newWidget);
//
//
//        $newWidget = new ImageMenuWidget();
//        $newWidget->icon = "iList.png";
//        $newWidget->title = "Owner Tables";
//        $newWidget->linkURL = 'SBD/OwnerTables';
//        $newWidget->description = "";
//        $UI->content->addControl($newWidget);


        #---- Finalize and Display
        $UI->display();
        }

        return $return;

    }
}


$phpAnvil->controller['dev.dashboard'] = new DashboardController();

?>