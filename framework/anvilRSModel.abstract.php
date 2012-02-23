<?php
require_once 'anvilModel.abstract.php';


abstract class anvilRSModelAbstract extends anvilModelAbstract
{
	const RECORD_STATUS_ACTIVE		= 1;
	const RECORD_STATUS_DISABLED	= 2;
	const RECORD_STATUS_DELETED		= 3;

    private $_rsName = array(
        'Unknown',
        'Active',
        'Disabled',
        'Deleted'
    );

//    public $addDTS;
//    public $addSourceTypeID;
//    public $addSourceID;
//
//    public $recordStatusID;
//    public $recordStatusDTS;
//    public $recordStatusSourceTypeID;
//    public $recordStatusSourceID;
//
//    public $importDTS;
//    public $importSourceTypeID;
//    public $importSourceID;
//
//    public $primaryTable;


    public function __construct($primaryTableName = '', $primaryFieldName = 'id')
    {
        parent::__construct($primaryTableName, $primaryFieldName);

        $this->fields->addDTS->fieldName = 'add_dts';
        $this->fields->addDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->addSourceTypeID->fieldName = 'add_source_type_id';
        $this->fields->addSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->addSourceID->fieldName = 'add_source_id';
        $this->fields->addSourceID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->recordStatusID->fieldName = 'record_status_id';
        $this->fields->recordStatusID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->recordStatusID = self::RECORD_STATUS_ACTIVE;

        $this->fields->recordStatusDTS->fieldName = 'record_status_dts';
        $this->fields->recordStatusDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->recordStatusSourceTypeID->fieldName = 'record_status_source_type_id';
        $this->fields->recordStatusSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->recordStatusSourceID->fieldName = 'record_status_source_id';
        $this->fields->recordStatusSourceID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->importDTS->fieldName = 'import_dts';
        $this->fields->importDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->importSourceTypeID->fieldName = 'import_source_type_id';
        $this->fields->importSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->importSourceID->fieldName = 'import_source_id';
        $this->fields->importSourceID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

	}


	public function getRSName($recordStatusID = 0)
    {
        if ($recordStatusID === 0) {
            return $this->_rsName[$this->recordStatusID];
        } else {
            return $this->_rsName[$recordStatusID];
        }

	}


	public function isActive()
    {
		return $this->recordStatusID == self::RECORD_STATUS_ACTIVE;
	}


	public function isDisabled()
    {
		return $this->recordStatusID == self::RECORD_STATUS_DISABLED;
	}


	public function isDeleted()
    {
		return $this->recordStatusID == self::RECORD_STATUS_DELETED;
	}


    public function setRecordStatus($newStatus)
    {
        $return = true;

        $this->recordStatusID = $newStatus;
        if (!$this->isNew()) {
            $return = $this->save();
        }

        return $return;
    }


	#---- Flag the Data Record as Deleted
	public function delete($sql = '')
    {
		return $this->setRecordStatus(self::RECORD_STATUS_DELETED);
	}


	#---- Flag the Data Record as Disabled
	public function disable()
    {
        return $this->setRecordStatus(self::RECORD_STATUS_DISABLED);
	}


	#---- Flag the Data Record as Active
	public function enable()
    {
        return $this->setRecordStatus(self::RECORD_STATUS_ACTIVE);
	}


	public function save($sql = '', $id_sql = '')
    {
		global $phpAnvil;

        $now = new DateTime(null, $phpAnvil->regional->dateTimeZone);

//		if ($this->isNew() && $this->addSourceID == 0) {
        if ($this->isNew()) {
//			$this->addDTS = date('Y-m-d H:i:s');
            $this->addDTS = $now->format($phpAnvil->regional->dtsFormat);

            if (empty($this->addSourceID)) {
                $this->addSourceTypeID = SOURCE_TYPE_USER;
       			$this->addSourceID = $phpAnvil->application->user->id;
            }
            if (empty($this->recordStatusID)) {
                $this->recordStatusID = self::RECORD_STATUS_ACTIVE;
            }
		}

//        if ($this->fields->field('recordStatusID')->changed)
        if ($this->fields->recordStatusID->changed)
        {
            $this->recordStatusDTS = $now->format($phpAnvil->regional->dtsFormat);
            $this->recordStatusSourceTypeID = SOURCE_TYPE_USER;
            $this->recordStatusSourceID = $phpAnvil->application->user->id;

        }

		return parent::save($sql, $id_sql);
	}


	#---- Flag the Data Record as Active
	public function unDelete() {
		return $this->enable();
	}

}


?>