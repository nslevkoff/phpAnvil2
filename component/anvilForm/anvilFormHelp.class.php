<?php

require_once 'anvilFormControl.abstract.php';


/**
* phpAnvil Form Help Control
*
* @copyright 	Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
*/
class anvilFormHelp extends anvilFormControlAbstract {

	const VERSION        = '1.0.0';

    const TYPE_BLOCK = 0;
    const TYPE_INLINE = 1;

    private $_typeClass = array(
        'help-block',
        'help-inline'
    );

    private $_typeTag = array(
        'p',
        'span'
    );

    public $type = self::TYPE_BLOCK;
    public $text;


	public function __construct($id = '', $text = '', $type = self::TYPE_BLOCK, $properties = null) {

        $this->enableLog();

		parent::__construct($id, '', $properties);

        $this->text = $text;
		$this->type = $type;
	}

	public function renderContent()
    {

        //---- Opening
        $return = '<' . $this->_typeTag[$this->type];

        //---- Class
        $return .= ' class="' . $this->_typeClass[$this->type];

        if (!empty($this->class)) {
            $return .= ' ' . $this->class;
        }

        $return .= '"';

        //---- Style
        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }

        $return .= '>';

        //---- Text
        $return .= $this->text;

        //---- Closing
        $return .= '</' . $this->_typeTag[$this->type] . '>';

		return $return;
	}
}

?>