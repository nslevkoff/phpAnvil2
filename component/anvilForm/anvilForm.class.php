<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';


/**
 * Form Control
 *
 * @copyright       Copyright (c) 2010-2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilForm extends anvilContainer
{
    const VERSION = '1.0.1';

    const METHOD_GET  = 'get';
    const METHOD_POST = 'post';

    private $_typeClass = array(
        'form-horizontal',
        'form-inline',
        'form-search',
        'form-vertical',
        'form-stacked'
    );

    const TYPE_DEFAULT    = 0;
    const TYPE_HORIZONTAL = 0;
    const TYPE_INLINE     = 1;
    const TYPE_SEARCH     = 2;
    const TYPE_VERTICAL   = 3;
    const TYPE_STACKED    = 4;

    public $action;
    public $actions;
    public $defaultButtonID;
    public $encType;
    public $method = self::METHOD_POST;
    public $headerEnabled = true;
    public $bodyEnabled = true;
    public $footerEnabled = true;
    public $target;

    public $validation = true;


    public function __construct($id = '', $method = self::METHOD_POST, $action = '', $type = self::TYPE_DEFAULT, $properties = null)
    {
        parent::__construct($id, $properties);

        $this->method = $method;
        $this->action = $action;
        $this->type   = $type;

        $this->actions = new anvilContainer();
    }


//    protected function preRenderControl($control)
//    {
//        if ($this->defaultButtonID && is_subclass_of($control, 'anvilFormControl')) {
//            $control->defaultButtonID = $this->defaultButtonID;
//        }
//    }


    public function renderContent()
    {
        $return = '';
        if ($this->headerEnabled) {
            $return .= $this->renderHeader();
        }

        if ($this->bodyEnabled) {
            $return .= $this->renderControls();

            $actions = $this->actions->renderControls();

            if (!empty($actions)) {
                $return .= '<div class="form-actions">';
                $return .= $actions;
                $return .= '</div>';
            }

        }

        if ($this->footerEnabled) {
            $return .= $this->renderFooter();
        }

        return $return;
    }


    public function renderHeader()
    {
        $return = '<form';

        if ($this->id) {
            $return .= ' id="' . $this->id . '"';
        }

        if ($this->method) {
            $return .= ' method="' . $this->method . '"';
        }
        /*		if ($this->action) {*/ #-- Always add action to the form even if blank. ~David~
        $return .= ' action="' . $this->action . '"';
        /*		}*/

        $return .= ' class="';

        if (is_numeric($this->type)) {
            $return .= $this->_typeClass[$this->type];

        } else {
            $return .= $this->type;
        }

        if ($this->class) {
            $return .= ' ' . $this->class;
        }

        if ($this->validation) {
            $return .= ' form-validation';
        }
        $return .= '"';

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }


        if ($this->encType) {
            $return .= ' enctype="' . $this->encType . '"';
        }

        if ($this->target) {
            $return .= ' target="' . $this->target . '"';
        }

        $return .= '>';


        return $return;
    }


    public function renderFooter()
    {
        $return = '</form>';

        return $return;
    }

}

?>