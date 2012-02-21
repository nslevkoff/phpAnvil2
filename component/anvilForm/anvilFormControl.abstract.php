<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools
*/


require_once PHPANVIL2_COMPONENT_PATH .'anvilControl.abstract.php';


/**
* Base Form Control Abstract Class
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
abstract class anvilFormControlAbstract extends anvilControlAbstract {

	const VERSION        = '1.0';


	public $defaultButtonID;
	public $name;
    public $label;


	public function __construct($id = '', $name = '', $properties = null) {
//		$this->_traceEnabled = $traceEnabled;

//		unset($this->defaultButtonID);
//		unset($this->name);
//        unset($this->label);


//		$this->addProperty('defaultButtonID', '');
//		$this->addProperty('name', '');
//        $this->addProperty('label', '');

		$this->name = $name;

		parent::__construct($id, $properties);
	}

    public function renderLabel()
    {
        $return = '';

        if ($this->label <> '')
        {
            $return .= '<label';
            if ($this->id <> '')
            {
                $return .= ' for="' . $this->id . '"';
            }
            $return .= '>' . $this->label . "</label>\n";
        }

        return $return;
    }


	public function renderPreClientScript() {
		$return = '';
		$return .= parent::renderPreClientScript();
		return $return;
	}

	public function renderPostClientScript() {
		$return = '';
		$return .= parent::renderPostClientScript();
		return $return;
	}

}

?>