<?php
require_once 'anvilContainer.class.php';
require_once 'anvilLink.class.php';
require_once 'anvilLiteral.class.php';
require_once 'anvilNavDropdown.class.php';
require_once 'anvilNavItem.class.php';


/**
 * phpAnvil Nav Control
 *
 * @copyright     Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilButtonGroup extends anvilContainer
{

    //---- Align ---------------------------------------------------------------
    const ALIGN_DEFAULT = 0;
    const ALIGN_LEFT  = 1;
    const ALIGN_RIGHT = 2;

    private $_alignClass = array(
        '',
        'pull-left',
        'pull-right'
    );

    public $align = self::ALIGN_DEFAULT;

    //---- Sizes ---------------------------------------------------------------
    const SIZE_DEFAULT = 0;
    const SIZE_MINI = 1;
    const SIZE_SMALL = 2;
    const SIZE_LARGE = 3;

    private $_sizeClass = array(
        '',
        'btn-mini',
        'btn-small',
        'btn-large'
    );


    /** @var anvilContainer */
    public $append;

    /** @var anvilContainer */
    public $prepend;

    public $appendText;
    public $prependText;

    public $size = anvilLink::SIZE_DEFAULT;


    public function __construct($id = '', $size = self::SIZE_DEFAULT, $properties = null)
    {

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->size = $size;

        $this->prepend = new anvilContainer();
        $this->append = new anvilContainer();

    }

    public function addLink($text, $url = '', $checked = false, $type = anvilLink::TYPE_TOGGLE, $properties = null)
    {
        $objLink = new anvilLink('', $text, $url, $type, $this->size, $properties);
        $objLink->checked = $checked;

        $this->addControl($objLink);

        return $objLink;
    }

    public function renderContent()
    {

//        $appendHTML = '';
//        $prependHTML = '';
        $appendHTML = $this->append->renderContent();
        $prependHTML = $this->prepend->renderContent();


        //---- Opening Tag
        $return = '<div';

        //---- ID
        if (!empty($this->id)) {
            $return .= ' id="' . $this->id . '"';
        }

        //---- Class
        $return .= ' class="btn-group';

        $return .= ' ' . $this->_sizeClass[$this->size];

        if ($this->align != self::ALIGN_DEFAULT) {
            $return .= ' ' . $this->_alignClass[$this->align];
        }

        if (!empty($appendHTML) || !empty($this->appendText)) {
            $return .= ' btn-group-append';
//            $return .= ' input-append';
        }

        if (!empty($prependHTML) || !empty($this->prependText)) {
            $return .= ' btn-group-prepend';
//            $return .= ' input-prepend';
        }

        $return .= '">';

        $return .= $prependHTML;

        if (!empty($this->prependText)) {
            $return .= '<span class="add-on">' . $this->prependText . '</span>';
        }

        $return .= $this->renderControls();


        if (!empty($this->appendText)) {
            $return .= '<span class="add-on">' . $this->appendText . '</span>';
        }

        $return .= $appendHTML;

        $return .= '</div>';


        return $return;
    }

}

?>