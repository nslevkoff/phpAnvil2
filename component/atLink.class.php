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


require_once('atControl.abstract.php');


/**
* Link Control
*
* @version		1.2
* @date			9/29/2011
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2011 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class atLink extends atControlAbstract {

	const VERSION        = '1.2';


	public $text;
	public $url;
	public $onClick;
    public $layers;
	public $confirmMessage;
//    public $confirmURL;

	
	public function __construct($id = '', $text = 'click here', $url = '', $class = '', $properties = null, $traceEnabled = false) {

//        $this->enableLog();

		unset($this->text);
		unset($this->url);
		unset($this->onClick);
        unset($this->layers);
        unset($this->confirmMessage);
//        unset($this->confirmURL);

		
		$this->addProperty('text', '');
		$this->addProperty('url', '');
		$this->addProperty('onClick', '');
        $this->addProperty('layers', 1);
        $this->addProperty('confirmMessage', '');
//        $this->addProperty('confirmURL', '');

		parent::__construct($id, $properties, $traceEnabled);

		$this->text = $text;
		$this->url = $url;
		$this->class = $class;
	}
	

	public function renderContent()
    {
        $render = $this->renderClientScript();
        $render .= "\n";
		$render .= '<a';

		if ($this->id) {
			$render .= ' id="' . $this->id . '"';
		}

//		$triggers = $this->renderTriggers();
//		$render .= $triggers;

		if ($this->onClick) {
			$render .= ' onclick="' . $this->onClick . '"';
		}

		if (empty($this->confirmMessage) && $this->url) {
			$render .= ' href="' . $this->url . '"';
		} else {
            $render .= ' href="javascript:void(0);"';
        }

		if ($this->class) {
			$render .= ' class="' . $this->class . '"';
		}
		$render .= '>';

        if ($this->layers > 1) {
            $render .= '<span>';
        }

		if ($this->text) {
			$render .= $this->text;
		}

        if ($this->layers > 1) {
            $render .= '</span>';
        }

		$render .= '</a>';

		return $render;
	}

	

    public function renderClientScript()
    {
//        global $phpAnvil;

        $return = '';

        if ($this->id && !empty($this->confirmMessage))
        {

            $return .= '<script>' . "\n";
            $return .= "\t" . '$(document).ready(function(){' . "\n";
            $return .= "\t\t" . '$("#' . $this->id . '").click(function(e){' . "\n";
            $return .= "\t\t\t" . 'e.preventDefault();' . "\n";
            $return .= "\t\t\t" . 'if(confirm("' . $this->confirmMessage . '"))' . "\n";
            $return .= "\t\t\t" . '{' . "\n";

//            $url = (!empty($this->confirmURL) ? $this->confirmURL : '#');
            $url = (!empty($this->url) ? $this->url : '#');

            $return .= "\t\t\t\t" . 'document.location = "' . $url . '";' . "\n";
            $return .= "\t\t\t" . '}' . "\n";
            $return .= "\t\t" . '});' . "\n";
            $return .= "\t" . '});' . "\n";
            $return .= '</script>' . "\n";
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