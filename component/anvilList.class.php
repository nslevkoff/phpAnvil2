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

require_once('anvilContainer.class.php');


/**
* List Container Control
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilList extends anvilContainer {

	const VERSION        	= '1.0';


	const TYPE_BULLET = 1;
	const TYPE_ORDERED = 2;

	protected $controlClass = array();
    protected $controlID = array();

	
	public $type;
	

	public function __construct($id = '', 
								$type = self::TYPE_BULLET, 
								$class = '', 
								$properties = array(), 
								$traceEnabled = false) 
	{
		unset($this->type);
		
		
//		$this->addProperty('class', '');
		$this->addProperty('type', self::TYPE_BULLET);

		parent::__construct($id, $properties, $traceEnabled);

		$this->type = $type;
		$this->class = $class;
	}

	public function addControl($control, $class = '', $id = '') {
		$this->controlClass[] = $class;
        $this->controlID[] = $id;
		$this->controls->add($control);
	}

	public function renderContent() {
		$return = '';

		if ($this->controls->count() > 0) {
			if ($this->type == self::TYPE_BULLET) {
				$return .= '<ul';
			} else {
				$return .= '<ol';
			}

			if ($this->id) {
				$return .= ' id="' . $this->id . '"';
			}

			if ($this->class) {
				$return .= ' class="' . $this->class . '"';
			}

			$return .= '>';

			$return .= $this->renderControls();

			if ($this->type == self::TYPE_BULLET) {
				$return .= '</ul>';
			} else {
				$return .= '</ol>';
			}
		}

		return $return;
	}

	public function renderControls() {
		$return = '';
		$controlNum = 0;
		for($this->controls->moveFirst(); $this->controls->hasMore(); $this->controls->moveNext()) {

			$objControl = $this->controls->current();
			$this->preRenderControl($objControl);
			$return .= '<li';

            if (!empty($this->controlID[$controlNum])) {
                $return .= ' id="' . $this->controlID[$controlNum] . '"';
            }

			if (!empty($this->controlClass[$controlNum])) {
				$return .= ' class="' . $this->controlClass[$controlNum] . '"';
			}
			$return .= '>' . $objControl->render() . '</li>';

			$controlNum++;
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