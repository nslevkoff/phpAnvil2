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


require_once('anvilFormControl.abstract.php');


/**
* Combo Box
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools
*/
class anvilComboBox extends anvilFormControlAbstract {

	const VERSION        	= '1.0';

	protected $_preItems = array();
	protected $_postItems = array();
	public $recordset;


	public $dataValue;
	public $dataName;
	public $directory;
    public $directoryRegEx;
	public $postbackEnabled;
	public $value;
    public $wrapEnabled = false;


	public function __construct($id = '', $name = '', $value = '', $properties = array(), $traceEnabled = false) {
//		$this->_traceEnabled = $traceEnabled;

//        $this->enableLog();

		unset($this->dataValue);
		unset($this->dataName);
		unset($this->directory);
        unset($this->directoryRegEx);
		unset($this->postbackEnabled);
		unset($this->value);
        unset($this->wrapEnabled);


		$this->addProperty('dataValue', 'id');
		$this->addProperty('dataName', 'name');
		$this->addProperty('directory', null);
        $this->addProperty('directoryRegEx', null);
		$this->addProperty('postbackEnabled', false);
//		$this->addProperty('recordset', null);
		$this->addProperty('value', '');
        $this->addProperty('wrapEnabled', false);
        $this->addProperty('wrapClass', 'selectWrap');

		$this->value = $value;

		parent::__construct($id, $name, $properties, $traceEnabled);
	}

	public function addPreItem($value, $name) {
		$this->_preItems[$value] = $name;
	}

	public function addPostItem($value, $name) {
		$this->_postItems[$value] = $name;
	}


	public function renderContent() {
//		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'rendering...', self::TRACE_TYPE_DEBUG);

        $return = '';

        if ($this->wrapEnabled) {
            $return .= '<p class="' . $this->wrapClass . '">';
        }

        $return .= $this->renderLabel();

		#---- Render the Combo Box Starting Tag
		$return .= '<select';

		if ($this->id) {
			$return .= ' id="' . $this->id . '"';
		}

		if ($this->name) {
			$return .= ' name="' . $this->name . '"';
		}

        if ($this->class) {
            $return .= ' class="' . $this->class . '"';
        }

        if ($this->style) {
            $return .= ' style="' . $this->style . '"';
        }


		$triggers = $this->renderTriggers();

		$this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $triggers, self::TRACE_TYPE_DEBUG);

//		$return .= $this->renderTriggers();
		$return .= $triggers;



/*
		if ($this->_enableAjax) {
			$return .= ' onChange="call_' . $this->_name . '();"';
		} elseif ($this->_enablePostBack) {
			$return .= " onChange=\"this.form.pbf.value='" . $this->_name . "';this.form.submit();\"";
		} elseif (isset($this->_lookupURL)) {
			$return .= ' onChange="call_' . $this->_name . '();"';
		} elseif (isset($this->_onChange)) {
			$return .= ' onChange="' . $this->_onChange . '"';
		}
*/

//		if ($this->onClick) {
//			$return .= ' onClick="' . $this->onClick . '"';
//		}

//		if ($this->defaultButtonID) {
//			$return .= ' onkeypress="enterSubmit(event, \'' . $this->defaultButtonID . '\');"';
//		}

		$return .= '>';

		#---- Render Pre Added Items
        $preItemCount = count($this->_preItems);

//        FB::log($this->name,'Name');
//        FB::log($preItemCount,'$preItemCount');

        reset($this->_preItems);
		for ($i = 0; $i < $preItemCount; $i++)
		{
			$return .= '<option value="' . key($this->_preItems) . '"';

			if ($this->value == key($this->_preItems)) {
				$return .= ' selected="selected"';
			}


			$return .= '>' . $this->_preItems[key($this->_preItems)] . "</option>\n";


			next($this->_preItems);
		}

//        FB::log($return, '$return');

//        fb::log($this->name, 'ComboBox Name');
//        fb::log($this->recordset, 'Recordset');

		#---- Render the ComboBox Items	from SQL Data
		if ($this->recordset) {
//			DevTrace::add(__FILE__, __METHOD__, __LINE__, '[' . $this->_id . '] Processing SQL...');

//			$objRS = $this->_devData->execute($this->_sql);

			$objRS = $this->recordset;

			if ($objRS->read()) {
				do {
					/*
					for($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext()) {
						$objColumn = $objRS->columns->current();
						if (!array_key_exists($objColumn->name, $this->_hideColumn)) {
							$return .= '<td class="' . $this->_cellClass . '">' . $objRS->data($objRS->columns->getIndex()) . '</td>';
						}
					}
					$return .= '</tr>';
					*/

					$return .= '<option value="' . $objRS->data($this->dataValue) . '"';

					if ($this->value == $objRS->data($this->dataValue)) {
						$return .= ' selected="selected"';
					}


					$return .= '>' . $objRS->data($this->dataName) . "</option>\n";

				} while($objRS->read());

				$objRS->close();

			}

		} elseif ($this->directory) {
//			DevTrace::add(__FILE__, __METHOD__, __LINE__, '[' . $this->_id . '] Processing Directory Files...');
//			DevTrace::add(__FILE__, __METHOD__, __LINE__, '[' . $this->_id . '] $this->_dir=' . $this->_dir, self::TRACE_TYPE_DEBUG);
//			DevTrace::add(__FILE__, __METHOD__, __LINE__, '[' . $this->_id . '] realpath=' . realpath($this->_dir), self::TRACE_TYPE_DEBUG);
//			DevTrace::add(__FILE__, __METHOD__, __LINE__, '[' . $this->_id . '] dirname=' . dirname($this->_dir), self::TRACE_TYPE_DEBUG);

			if ($handle = opendir($this->directory))
            {
                $filterFiles = isset($this->directoryRegEx);
                $files = array();

//				while (false !== ($file = readdir($handle)))
//                {
//					if ($file != '.' && $file != '..')
//                    {
//                        if (!$filterFiles || preg_match($this->directoryRegEx, $file) > 0)
//                        {
//						    $return .= '<option value="' . $file . '"';

//						    if ($this->value == $file) {
//							    $return .= ' selected="selected"';
//						    }

//						    $return .= '>' . $file . "</option>\n";
//                        }
//					}
//				}
//				closedir($handle);

                while (false !== ($file = readdir($handle)))
                {
                    if ($file != '.' && $file != '..')
                    {
                        if (!$filterFiles || preg_match($this->directoryRegEx, $file) > 0)
                        {
                            $files[] = $file;
                        }
                    }
                }
                closedir($handle);
                sort($files);

                $count = count($files);
                for ($i = 0; $i < $count; $i++)
                {
                    $return .= '<option value="' . $files[$i] . '"';

                    if ($this->value == $files[$i]) {
                        $return .= ' selected="selected"';
                    }

                    $return .= '>' . $files[$i] . "</option>\n";
                }
            }

		}

		#---- Render Post Added Items
		for ($i = 0; $i < count($this->_postItems); $i++)
		{
			$return .= '<option value="' . key($this->_postItems) . '"';

			if ($this->_selectedID == key($this->_postItems)) {
				$return .= ' selected="selected"';
			}


			$return .= '>' . $this->_postItems[key($this->_postItems)] . "</option>\n";


			next($this->_postItems);
		}

		#---- Render the Combo Box Ending Tag
		$return .= '</select>' . "\n";

//        fb::log($return);
        if ($this->wrapEnabled) {
            $return .= '</p>';
        }

		return $return;
	}

	public function renderPreClientScript()
    {
		$return = '';

//        fb::log($this->id, 'renderPreClientScript()');

		if ($this->postbackEnabled)
        {
            $this->_logDebug('Adding onchange trigger...');

			$this->addTrigger('onchange', 'object.form.submit();');
		}

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