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


require_once('anvilObject.abstract.php');


static $alreadyRendered = array();

function isRendered($key)
{
    global $alreadyRendered;

    return array_key_exists($key, $alreadyRendered);
}


function renderOnce($key)
{
    global $alreadyRendered;

    $alreadyRendered[$key] = true;
}


/**
 * Base Control Object Class
 *
 * @version         1.0
 * @date            8/24/2010
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools
 */
abstract class anvilControlAbstract extends anvilObjectAbstract
{
    const VERSION = '1.0';

    protected $_triggers = array();

    //---- Define Properties for Documentation and IDE Use

    /**
     * CSS Class Name
     *
     * @var string $class
     */
    public $class;

    /**
     * HTML ID to use for the rendered control.
     *
     * @var string $id
     */
    public $id;

    /**
     * Custom CSS style for the rendered control.
     *
     * @var string $style
     */
    public $style;

    /**
     * Javascript to render at the top of the page output.
     *
     * @var string $preClientScript
     */
    public $preClientScript;

    /**
     * Javascript to render at the bottom of the page output.
     *
     * @var string $postClientScript
     */
    public $postClientScript;

    /**
     * anvilTemplate object for rendering the control.
     *
     * @var anvilTemplateAbstract
     */
    public $anvilTemplate;

    /**
     * Outer template filename to use for rendering the control inside of.
     *
     * @var string $outerTemplate
     */
    public $outerTemplate;

    /**
     * Variable ID to use for rendering the control inside the outer template.
     *
     * @var string $outerTemplateID
     */
    public $outerTemplateID;

    /** @var phpAnvil2 */
    protected $_core;

    public $dataPlacement;


    public function __construct($id = null, $properties = null)
    {
        parent::__construct($properties);

        global $phpAnvil;

        $this->_core = $phpAnvil;

        //		$this->enableTrace();

        //---- Unset defined properties before defining virtual versions
        //---- of the properties
        //		unset($this->class);
        //		unset($this->id);
        //		unset($this->style);
        //		unset($this->preClientScript);
        //		unset($this->postClientScript);
        //		unset($this->anvilTemplate);
        //		unset($this->outerTemplate);
        //		unset($this->outerTemplateID);


        //---- Define virtual properties.
        //		$this->addProperty('class', '');
        //		$this->addProperty('id', 0);
        //		$this->addProperty('style', '');
        //
        //		$this->addProperty('preClientScript', '');
        //		$this->addProperty('postClientScript', '');
        //
        //		$this->addProperty('anvilTemplate', null);
        //		$this->addProperty('outerTemplate', '');
        //		$this->addProperty('outerTemplateID', '');

        $this->id = $id;
        //		$this->setProperties($properties);

        //		return parent::__construct($properties);

        //        $this->enableLog();
    }


    public function addTrigger($event, $code)
    {
        $this->_logDebug('Adding trigger...');
        $this->_logDebug($event, '$event');
        $this->_logDebug($code, '$code');


        $this->_triggers[$event][] = $code;

        $this->_logDebug($this->_triggers, '$this->_triggers');
    }


    public function render($anvilTemplate = null)
    {
        $return = '';
        if (is_object($anvilTemplate)) {
            $this->anvilTemplate = $anvilTemplate;
        }
        if ($this->outerTemplate != '' && is_object($anvilTemplate)) {
            $return .= $this->renderTemplate();
        } else {
            $return .= $this->renderContent();
        }
        return $return;
    }


    public function renderContent()
    {
        return '';
    }


    public function renderTemplate()
    {
        $return = '';
        $_anvilTemplate = '';

        if (is_object($this->anvilTemplate)) {
            $_anvilTemplate = clone $this->anvilTemplate;
        }

        if (is_object($_anvilTemplate)) {
            if ($this->outerTemplateID != '') {
                $_anvilTemplate->assign($this->outerTemplateID, $this->renderContent());
            } else {
                $_anvilTemplate->assign('anvilControl', $this->renderContent());
            }
            $return .= $_anvilTemplate->render($this->outerTemplate);
        }

        return $return;
    }


    public function renderTriggers()
    {
        $return = '';

        $this->_logDebug('Rendering triggers...');

        $this->_logDebug($this->_triggers, '$this->_triggers');

        foreach (array_keys($this->_triggers) as $event)
        {
            $return .= ' ' . $event . '="' . $this->id . '_' . $event . '(this);"';
        }

        //        fb::log($return, 'Triggers');
        $this->_logDebug($return, 'Triggers');

        return $return;
    }


    public function renderPreClientScript()
    {
        $return = '';

        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'rendering...', self::TRACE_TYPE_DEBUG);

        if (count($this->_triggers) > 0) {
            $return .= '<script  type="text/javascript">' . "\n";

            foreach (array_keys($this->_triggers) as $event)
            {
                $return .= 'function ' . $this->id . '_' . $event . '(object) {' . "\n";

                foreach (array_values($this->_triggers[$event]) as $code)
                {
                    $return .= "\t" . $code . "\n";
                }

                $return .= '}' . "\n";
            }

            $return .= "</script>\n";
        }

        return $return;
    }


    public function renderPostClientScript()
    {
        return '';
    }

}

?>