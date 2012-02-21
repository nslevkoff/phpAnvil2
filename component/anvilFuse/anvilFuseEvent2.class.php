<?php
/**
 * @file
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @license
 *                  This source file is subject to the new BSD license that is
 *                  bundled with this package in the file LICENSE.txt. It is also
 *                  available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup         phpAnvilTools anvilFuse
 */


require_once PHPANVIL2_COMPONENT_PATH . 'anvilForm/anvilFormObject.abstract.php';


/**
 * anvilFuse Event Class
 *
 * @version         1.0
 * @date            8/26/2010
 * @author          Nick Slevkoff <nick@slevkoff.com>
 * @copyright       Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
 * @ingroup         phpAnvilTools anvilFuse
 */
class anvilFuseEvent2 extends anvilFormObjectAbstract
{
    public $dts;
    public $fuseEventTypeID;
    public $fuseApplicationID;
    public $version;
    public $userIP;
    public $userID;
    public $name;
    public $number;
    public $details;
    public $file;
    public $line;
    public $trace;


    public function __construct($dataConnection, $table, $id = 0)
    {
//        global $phpAnvil;

        //        $this->enableLog();

//        $this->addProperty('id', $table, 'fuse_event_id', self::DATA_TYPE_NUMBER);
//        $this->addProperty('dts', $table, 'dts', self::DATA_TYPE_ADD_DTS);
//        $this->addProperty('fuseEventTypeID', $table, 'fuse_event_type_id', self::DATA_TYPE_NUMBER);
//        $this->addProperty('fuseApplicationID', $table, 'fuse_application_id', self::DATA_TYPE_NUMBER);
//        $this->addProperty('version', $table, 'version', self::DATA_TYPE_STRING);
//        $this->addProperty('userIP', $table, 'user_ip', self::DATA_TYPE_STRING);
//        $this->addProperty('userID', $table, 'user_id', self::DATA_TYPE_NUMBER);
//        $this->addProperty('name', $table, 'name', self::DATA_TYPE_STRING);
//        $this->addProperty('number', $table, 'number', self::DATA_TYPE_STRING);
//        $this->addProperty('details', $table, 'details', self::DATA_TYPE_STRING);
//        $this->addProperty('file', $table, 'file', self::DATA_TYPE_STRING);
//        $this->addProperty('line', $table, 'line', self::DATA_TYPE_STRING);
//        $this->addProperty('trace', $table, 'trace', self::DATA_TYPE_STRING);

        //		parent::__construct($anvilDataConnection, $table);
        parent::__construct($table);

        $this->properties->id->fieldName = 'fuse_event_id';

        $this->dataConnection = $dataConnection;
        $this->id = $id;

        //        $this->_logDebug($this);
    }
}

?>