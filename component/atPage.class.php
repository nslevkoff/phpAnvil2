<?php
/**
 * @file
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                  This source file is subject to the new BSD license that is
 *                  bundled with this package in the file LICENSE.txt. It is also
 *                  available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup         phpAnvilTools
 */


require_once('atContainer.class.php');
require_once('atPageHead.class.php');


/**
 * Page Container Control
 *
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools
 */
class atPage extends atContainer
{

    const VERSION = '1.1';


    public $atTemplate;
    public $head;

    private $_preClientScript;
    private $_postClientScript;

    //---- Old Properties (depreciated) ----------------------------------------
    public $browserTitle;
    public $pageTitle;


    public function __construct($browserTitle = '', $pageTitle = '', $properties = array(), $traceEnabled = false)
    {
        $this->head = new atPageHead();

        //---- Old Properties (depreciated) ----------------------------------------
        $this->browserTitle = $browserTitle;
        $this->pageTitle    = $pageTitle;

        parent::__construct(0, $properties, $traceEnabled);
    }


    public function assign($var, $value)
    {
        $this->atTemplate->assign($var, $value);
    }


    public function assignTokens()
    {
//        $preClientScript  = $this->renderPreClientScript();
//        $postClientScript = $this->renderPostClientScript();


        $this->head->render();
        $this->head->html .= $this->_preClientScript;
        //        $headHTML .= $preClientScript;
        //        $this->assign('headHTML', $headHTML);

        $this->assign('head', (array)$this->head);

        $this->assign('postClientScript', $this->_postClientScript);

        //---- Old Tokens (depreciated) ----------------------------------------
        $this->assign('browserTitle', $this->browserTitle);
//        $this->assign('pageTitle', $this->pageTitle);
        $this->assign('preClientScript', $this->_preClientScript);

    }


    public function displayControls()
    {
        //		$this->logDebug('Executing...');

        //        fb::log('atPage.displayControls()');
        $this->_preClientScript  = $this->renderPreClientScript();
        $this->_postClientScript = $this->renderPostClientScript();

        $return = '';
        for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
            $objControl = $this->controls->current();
            //			$this->logDebug('Display Control:id_' . $objControl->id);
            $this->preRenderControl($objControl);
            if ($this->innerTemplate != '' && is_object($this->atTemplate)) {

                $msg = 'Assign Control-Template:id_' . $objControl->id;
                //				$this->logDebug($msg);

                //        fb::log($msg);

                $this->assign('id_' . $objControl->id, $objControl->render($this->atTemplate));
            }
        }
        return $return;
    }


    public function display()
    {
        //		$this->logDebug('Executing...');
        if (is_object($this->atTemplate)) {
            //			$this->logDebug('Set Page Template');
            $this->atTemplate = clone $this->atTemplate;
        }
        $this->displayControls();

        $this->assignTokens();

        $this->atTemplate->display($this->innerTemplate);
    }


    public function displayPage()
    {
        $this->assignTokens();
    }


    public function render($atTemplate = null)
    {
        //		$this->logDebug('Executing...');

        if (is_object($this->atTemplate)) {
            //			$this->logDebug('Set Page atTemplate');
            $this->atTemplate = clone $this->atTemplate;
        }
        $this->assignTokens();
        $this->displayControls();

        return $this->atTemplate->render($this->innerTemplate);
    }


    public function renderPostClientScript()
    {
        $return = '';
        for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
            $objControl = $this->controls->current();
            $return .= $objControl->renderPostClientScript();
        }
        return $return;
    }


    public function renderPreClientScript()
    {
        $return = '';
        for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
            $objControl = $this->controls->current();
            $return .= $objControl->renderPreClientScript();
        }
        return $return;
    }
}

?>