<?php
require_once('atControl.abstract.php');


/**
 * Inline Label Control
 *
 * @copyright     Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 */
class atLabel extends atControlAbstract
{

    const TYPE_DEFAULT = '';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_IMPORTANT = 'important';
    const TYPE_INFO = 'info';

    const VERSION = '1.0';


    public $type;
    public $value;


    public function __construct($id = '', $value = '', $properties = null)
    {
        unset($this->type);
        unset($this->value);


        $this->addProperty('type', self::TYPE_DEFAULT);
        $this->addProperty('value', '');

        parent::__construct($id, $properties, false);

        $this->value = $value;

    }


    public function renderContent()
    {
        $return = '<span class="inlineLabel';
        if ($this->type != self::TYPE_DEFAULT) {
            $return .= ' inlineLabel-' . $this->type;
        }
        $return .= '">' . $this->value . '</span>';

        return $return;
    }
}
?>