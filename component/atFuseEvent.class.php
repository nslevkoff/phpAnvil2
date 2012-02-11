<?php
/**
* @file
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
* 	This source file is subject to the new BSD license that is
* 	bundled with this package in the file LICENSE.txt. It is also
* 	available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup 		phpAnvilTools atFuse
*/


require_once('atFormObject.abstract.php');


/**
* atFuse Event Class
*
* @version		1.0
* @date			8/26/2010
* @author		Nick Slevkoff <nick@slevkoff.com>
* @copyright 	Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup 		phpAnvilTools atFuse
*/
class atFuseEvent extends atFormObjectAbstract
{
	public function __construct($atDataConnection, $table, $id = 0) {
		$this->addProperty('id', 'event_id', self::DATA_TYPE_NUMBER, $id);
		$this->addProperty('dts', 'dts', self::DATA_TYPE_ADD_DTS);
		$this->addProperty('eventTypeID', 'event_type_id', self::DATA_TYPE_NUMBER ,0, 'etid');
		$this->addProperty('applicationID', 'application_id', self::DATA_TYPE_NUMBER ,0, 'aid');
		$this->addProperty('version', 'version', self::DATA_TYPE_STRING, null, 'ver');
		$this->addProperty('userIP', 'user_ip', self::DATA_TYPE_STRING, null, 'uip');
        $this->addProperty('userID', 'user_id', self::DATA_TYPE_NUMBER ,0, 'uid');
		$this->addProperty('name', 'name', self::DATA_TYPE_STRING, null, 'name');
		$this->addProperty('number', 'number', self::DATA_TYPE_STRING, null, 'num');
		$this->addProperty('details', 'details', self::DATA_TYPE_STRING, null, 'det');
		$this->addProperty('file', 'file', self::DATA_TYPE_STRING, null, 'file');
		$this->addProperty('line', 'line', self::DATA_TYPE_STRING, null, 'line');
		$this->addProperty('trace', 'trace', self::DATA_TYPE_STRING, null, 'trc');

		parent::__construct($atDataConnection, $table, $id, '');
	}
}

?>