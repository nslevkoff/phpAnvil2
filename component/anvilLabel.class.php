<?php
require_once('anvilControl.abstract.php');


/**
 * Inline Label Control
 *
 * @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilLabel extends anvilControlAbstract
{

    const TYPE_DEFAULT = 0;
    const TYPE_SUCCESS = 1;
    const TYPE_WARNING = 2;
    const TYPE_IMPORTANT = 3;
    const TYPE_INFO = 4;

    public $typeClass = array(
        '',
        'label-success',
        'label-warning',
        'label-important',
        'label-info'
    );


    public $type;
    public $value;


    public function __construct($id = '', $value = '', $type = self::TYPE_DEFAULT, $properties = null)
    {
//        unset($this->type);
//        unset($this->value);


//        $this->addProperty('type', self::TYPE_DEFAULT);
//        $this->addProperty('value', '');

        parent::__construct($id, $properties);

        $this->type = $type;
        $this->value = $value;
        $this->class = $this->typeClass[$this->type];

    }


    public function renderContent()
    {
        $return = '<span class="label';
        $return .= ' ' . $this->class;
        $return .= '">' . $this->value . '</span>';

        return $return;
    }
}
?>