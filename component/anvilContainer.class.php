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

require_once('anvilControl.abstract.php');
require_once('anvilCollection.class.php');


/**
 * Container Control
 *
 * @version        1.0
 * @author         Nick Slevkoff <nick@slevkoff.com>
 * @copyright      Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilContainer extends anvilControlAbstract
{

    const VERSION = '2.0';


    /**
     * Collection of children controls within the container.
     *
     * @var anvilCollection
     */
    public $controls = null;


    //---- Define Properties for Documentation and IDE Use

    /**
     * anvilTemplate to use for rendering inside the container.
     *
     * @var anvilTemplateAbstract
     */
    public $innerTemplate;


    public function __construct($id = 0, $properties = null)
    {

        parent::__construct($id, $properties);

        //        $this->enableLog();

        $this->controls = new anvilCollection();

    }


    public function addControl($control)
    {
        $this->controls->add($control);
    }


    public function preRenderControl($control)
    {
    }


    public function renderControls()
    {
//        $this->_logDebug('Executing...id_' . $this->id);
        $return         = '';
        $_anvilTemplate = null;
        if (is_object($this->anvilTemplate)) {
//            $this->_logDebug('clone anvilTemplate:id_' . $this->id);
            $_anvilTemplate = clone $this->anvilTemplate;
        }
        for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
            $objControl = $this->controls->current();
//            $this->_logDebug('Render Control:id_' . $objControl->id);
            $this->preRenderControl($objControl);
            if ($this->innerTemplate != '' && is_object($_anvilTemplate)) {

                $content = $objControl->render($this->anvilTemplate);
//                $this->_logDebug('Assign InnerTemplate:id_' . $objControl->id);
//                $this->_logDebug('Assign InnerTemplate:' . $content);
                $_anvilTemplate->assign('id_' . $objControl->id, $content);
            } else {
//                $this->_logDebug('Render Control:id_' . $objControl->id);
                $return .= $objControl->render($this->anvilTemplate);

            }
        }
        if ($this->innerTemplate != '' && is_object($_anvilTemplate)) {
//            $this->_logDebug('Render innerTemplate:' . $this->innerTemplate);
            $return .= $_anvilTemplate->render($this->innerTemplate);
//            $this->_logDebug('Render inner:' . $return);
        }

//        $this->_logDebug('return:' . $return);
        return $return;
    }


    public function renderContent()
    {
        $return = '';
        $return .= $this->renderControls();
        return $return;
    }


    public function renderPreClientScript()
    {
        //		$this->_logDebug('renderPreClientScript...id_' . $this->id);
        $return = '';
        if (!is_null($this->controls)) {
            for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
                $objControl = $this->controls->current();
                $return .= $objControl->renderPreClientScript();
            }
        }
        return $return;
    }


    public function renderPostClientScript()
    {
        //		$this->_logDebug('renderPostClientScript...id_' . $this->id);
        $return = '';
        if (!is_null($this->controls)) {
            for ($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {
                $objControl = $this->controls->current();
                $return .= $objControl->renderPostClientScript();
            }
        }
        return $return;
    }

}

?>