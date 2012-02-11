<?php

/**
* @file
* Record Status Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Pulse
*/

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base2.model.php');


abstract class RecordStatusModel2 extends BaseModel2
{
	const RECORD_STATUS_ACTIVE		= 10;
	const RECORD_STATUS_DISABLED	= 20;
	const RECORD_STATUS_DELETED		= 30;

    public $primaryTable;

    public function __construct(
        $atDataConnection,
        $dataFrom,
        $primaryTable = '',
        $dataFilter = '')
    {
        parent::__construct($atDataConnection, $dataFrom, $dataFilter);

        if (empty($primaryTable))
        {
            $this->primaryTable = $dataFrom;
        } else {
            $this->primaryTable = $primaryTable;
        }

        $this->addProperty('addDTS', $this->primaryTable, 'add_dts', self::DATA_TYPE_DTS, null, 20, true, false, '', false);
        $this->addProperty('addSourceTypeID', $this->primaryTable, 'add_source_type_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);
        $this->addProperty('addSourceID', $this->primaryTable, 'add_source_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);

        $this->addProperty('recordStatusID', $this->primaryTable, 'record_status_id', self::DATA_TYPE_NUMBER, self::RECORD_STATUS_ACTIVE, 6, true, false, '', false);
        $this->addProperty('recordStatusDTS', $this->primaryTable, 'record_status_dts', self::DATA_TYPE_DTS, null, 20, true, false, '', false);
        $this->addProperty('recordStatusSourceTypeID', $this->primaryTable, 'record_status_source_type_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);
        $this->addProperty('recordStatusSourceID', $this->primaryTable, 'record_status_source_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);

        $this->addProperty('importDTS', $this->primaryTable, 'import_dts', self::DATA_TYPE_DTS, null, 20, true, false, '', false);
        $this->addProperty('importSourceTypeID', $this->primaryTable, 'import_source_type_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);
        $this->addProperty('importSourceID', $this->primaryTable, 'import_source_id', self::DATA_TYPE_NUMBER, null, 6, true, false, '', false);

	}


	public function getRecordStatusName()
    {
		switch($this->recordStatusID) {
			case self::RECORD_STATUS_ACTIVE:
				return 'Active';
			case self::RECORD_STATUS_DELETED:
				return 'Deleted';
			case self::RECORD_STATUS_DISABLED:
				return 'Disabled';
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

//        FB::log($this, 'RecordStatus2 Model');

        $now = new DateTime(null, $phpAnvil->regional->dateTimeZone);

		if ($this->isNew() && $this->addSourceID == 0) {
//			$this->addDTS = date('Y-m-d H:i:s');
            $this->addDTS = $now->format($phpAnvil->regional->dtsFormat);
			$this->addSourceTypeID = SOURCE_TYPE_USER;
			$this->addSourceID = $phpAnvil->application->user->id;
		}

        if ($this->properties->property('recordStatusID')->changed)
        {
            $this->recordStatusDTS = $now->format($phpAnvil->regional->dtsFormat);
            $this->recordStatusSourceTypeID = SOURCE_TYPE_USER;
            $this->recordStatusSourceID = $phpAnvil->application->user->id;

//            FB::info($this->recordStatusDTS, 'recordStatusDTS');
//            FB::info($now);
        }

		return parent::save($sql, $id_sql);
	}


	#---- Flag the Data Record as Active
	public function unDelete() {
		return $this->enable();
	}

}


?>