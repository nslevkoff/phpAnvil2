<?php
/**
 * @file
 * @author        Jeff Pizano <jeff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup       Dev_Module Pulse_Controllers
 */
require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.controller.php');

/**
 * Web action to load admin users section for an account.
 *
 * @author        Jeff Pizano <jeff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dev_Module Pulse_Controllers
 */

class ListDemoController extends BaseController
{


    function init()
    {
        global $phpAnvil;

        $return = parent::init();

        if ($return)
        {
            $phpAnvil->loadWidget('ANDI', 'ui');
            $phpAnvil->loadWidget('ANDI', 'menu');
            $phpAnvil->loadWidget('dev','listmaker');
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
            $UI->pageTitle = 'Autoplay list demo';

//      #----------------- Content -----------------

        $objList = new ListMakerWidget();
        $objList->add(1,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 1</b></td></tr></table>');
        $objList->add(2,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 2</b></td></tr></table>');
        $objList->add(3,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 3</b></td></tr></table>');
        $objList->add(4,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 4</b></td></tr></table>');
        $objList->add(5,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 5</b></td></tr></table>');
//        $objList->add(6,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 6</b></td></tr></table>');
//        $objList->add(7,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 7</b></td></tr></table>');
//        $objList->add(8,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 8</b></td></tr></table>');
//        $objList->add(9,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 9</b></td></tr></table>');
//        $objList->add(10,'<table><tr><td><img width="22" height="22" src="https://www.andisolutions.com/f/1/fixed_advansync.png/90x90" /></td><td valign="middle"><b>&nbsp;Video Item 10</b></td></tr></table>');
        $UI->content->addControl($objList);

//      #---- Finalize and Display -----------------
          $UI->display();
        }

        return $return;

    }
}


$phpAnvil->controller['dev.listdemo'] = new ListDemoController();

?>