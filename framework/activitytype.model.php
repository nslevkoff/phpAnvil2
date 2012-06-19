<?php

define('SQL_TABLE_ACTIVITY_TYPE', SQL_TABLE_PREFIX . 'activity_type');

require_once 'anvilRSModel.abstract.php';

/**
 * @copyright    (c) 2012 signingTRAC.com
 *
 * @property string $name
 * @property int    $labelTypeID
 * @property int    $sequence
 */
class ActivityTypeModel extends anvilRSModelAbstract
{

    public function __construct($id = 0)
    {

        $this->_saveActivity = false;

        parent::__construct(SQL_TABLE_ACTIVITY_TYPE, 'activity_type_id');

        $this->fields->id->fieldName = 'activity_type_id';
        $this->fields->id->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->name->fieldName = 'name';

        $this->fields->labelTypeID->fieldName = 'label_type_id';
        $this->fields->labelTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->sequence->fieldName = 'sequence';
        $this->fields->sequence->fieldType = anvilModelField::DATA_TYPE_NUMBER;


        $this->id = $id;
    }
}

?>
