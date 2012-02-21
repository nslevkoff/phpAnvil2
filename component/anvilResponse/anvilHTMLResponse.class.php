<?php

require_once PHPANVIL2_COMPONENT_PATH . 'anvilAlert.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';

require_once 'anvilResponse.abstract.php';
require_once 'anvilHTMLResponseHead.class.php';



/**
 * phpAnvil HTML Response Abstract Control
 *
 * @copyright       Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilHTMLResponse extends anvilResponseAbstract
{

    const VERSION = '1.0';

    public $template;
    public $templateFilename;

//    public $alerts;
    public $head;
    public $page = array();

    private $_preClientScript;
    private $_postClientScript;


    public function __construct($properties = null)
    {
        //        $this->enableLog();

        $this->head = new anvilHTMLResponseHead();
        $this->alerts = new anvilContainer();

        parent::__construct($properties);
    }


    public function assign($var, $value)
    {
        $this->template->assign($var, $value);
    }


    public function assignTokens()
    {
        $this->head->render();
        $this->head->html .= $this->_preClientScript;

        $this->assign('head', (array)$this->head);

        $this->assign('postClientScript', $this->_postClientScript);

        $this->assign('page', $this->page);

//        $this->assign('alerts', $this->alerts->renderControls());

    }


    public function displayControls()
    {
        //		$this->_logDebug('Executing...');

        //        fb::log('anvilPage.displayControls()');
        $this->_preClientScript  = $this->renderPreClientScript();
        $this->_postClientScript = $this->renderPostClientScript();

        $return = '';
        for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
            $objControl = $this->controls->current();
            //			$this->_logDebug('Display Control:id_' . $objControl->id);
            $this->preRenderControl($objControl);
            if ($this->innerTemplate != '' && is_object($this->template)) {

                $msg = 'Assign Control-Template:id_' . $objControl->id;
                $this->_logDebug($msg);

                //        fb::log($msg);
                $html = $objControl->render($this->template);

                $this->_logDebug($html);

                $this->assign('id_' . $objControl->id, $html);
            }
        }
        return $return;
    }


    public function display()
    {
        //		$this->_logDebug('Executing...');
        if (is_object($this->template)) {
            //			$this->_logDebug('Set Page Template');
            $this->template = clone $this->template;
        }

        $this->displayControls();

        $this->assignTokens();

        $this->template->display($this->innerTemplate);
    }


    public function displayPage()
    {
        $this->assignTokens();
    }


    public function render($anvilTemplate = null)
    {
        //		$this->_logDebug('Executing...');

        if (is_object($this->template)) {
            //			$this->_logDebug('Set Page anvilTemplate');
            $this->template = clone $this->template;
        }
        $this->assignTokens();
        $this->displayControls();

        return $this->template->render($this->innerTemplate);
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