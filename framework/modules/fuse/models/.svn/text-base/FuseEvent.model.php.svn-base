<?php

require_once(PHPANVIL_FRAMEWORK_PATH . 'RecordStatus2.model.php');

/**
* Database table structure model for ANDI patient.
*
* @author       Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
*/

class FuseEventModel extends RecordStatusModel2
{
//    public $accountStageID;


    public function __construct($atDataConnection, $id = 0)
    {

//        unset($this->accountStageID);

        $this->addProperty('id', SQL_TABLE_FUSE_EVENTS, 'event_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('DTS', SQL_TABLE_FUSE_EVENTS, 'dts', self::DATA_TYPE_ADD_DTS);
        $this->addProperty('eventTypeID', SQL_TABLE_FUSE_EVENTS, 'event_type_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('applicationID', SQL_TABLE_FUSE_EVENTS, 'application_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('version', SQL_TABLE_FUSE_EVENTS, 'version', self::DATA_TYPE_STRING);
        $this->addProperty('userIP', SQL_TABLE_FUSE_EVENTS, 'user_ip', self::DATA_TYPE_STRING);
        $this->addProperty('userID', SQL_TABLE_FUSE_EVENTS, 'user_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('name', SQL_TABLE_FUSE_EVENTS, 'name', self::DATA_TYPE_STRING);
        $this->addProperty('number', SQL_TABLE_FUSE_EVENTS, 'number', self::DATA_TYPE_NUMBER);
        $this->addProperty('details', SQL_TABLE_FUSE_EVENTS, 'details', self::DATA_TYPE_STRING);
        $this->addProperty('file', SQL_TABLE_FUSE_EVENTS, 'file', self::DATA_TYPE_STRING);
        $this->addProperty('line', SQL_TABLE_FUSE_EVENTS, 'line', self::DATA_TYPE_NUMBER);
        $this->addProperty('trace', SQL_TABLE_FUSE_EVENTS, 'trace', self::DATA_TYPE_STRING);

        parent::__construct($atDataConnection, SQL_TABLE_FUSE_EVENTS);
        $this->id = $id;
    }

}

?>
