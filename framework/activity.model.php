<?php

define('SQL_TABLE_ACTIVITY', SQL_TABLE_PREFIX . 'activity');

require_once 'anvilRSModel.abstract.php';

/**
 * @copyright    (c) 2012 signingTRAC.com
 *
 * @property int    $accountID
 * @property int    $targetTableID
 * @property string $targetTableName
 * @property int    $targetID
 * @property int    $activityTypeID
 * @property string $description
 * @property string $detail
 */
class ActivityModel extends anvilRSModelAbstract
{
    const TYPE_ADDED     = 1;
    const TYPE_UPDATED   = 2;
    const TYPE_DISABLED  = 3;
    const TYPE_ENABLED   = 4;
    const TYPE_DELETED   = 5;
    const TYPE_IMPORTED  = 6;
    const TYPE_EXPORTED  = 7;
    const TYPE_PROCESSED = 8;
    const TYPE_ASSIGNED = 9;
    const TYPE_REMOVED = 10;


    public function __construct($id = 0)
    {

        $this->_saveActivity = false;

        parent::__construct(SQL_TABLE_ACTIVITY, 'activity_id');

        $this->fields->id->fieldName = 'activity_id';
        $this->fields->id->fieldType = anvilModelField::DATA_TYPE_INTEGER;

        $this->fields->accountID->fieldName = 'account_id';
        $this->fields->accountID->fieldType = anvilModelField::DATA_TYPE_INTEGER;

        $this->fields->targetTableID->fieldName = 'target_table_id';
        $this->fields->targetTableID->fieldType = anvilModelField::DATA_TYPE_INTEGER;

        $this->fields->targetTableName->fieldName = 'target_table_name';

        $this->fields->targetID->fieldName = 'target_id';
        $this->fields->targetID->fieldType = anvilModelField::DATA_TYPE_INTEGER;

        $this->fields->activityTypeID->fieldName = 'activity_type_id';
        $this->fields->activityTypeID->fieldType = anvilModelField::DATA_TYPE_INTEGER;

        $this->fields->description->fieldName = 'description';
        $this->fields->detail->fieldName = 'detail';

        $this->id = $id;
    }
}

?>
