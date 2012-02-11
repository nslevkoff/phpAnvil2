<?php

/**
* @file
* Record Status Model
*
* @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
* @copyright    (c) 2010 Solutions By Design
* @ingroup        Pulse
*/

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.model.php');


abstract class RecordStatusModel extends BaseModel {
	const RECORD_STATUS_ACTIVE		= 10;
	const RECORD_STATUS_DISABLED	= 20;
	const RECORD_STATUS_DELETED		= 30;

	public function __construct($atDataConnection, $dataFrom, $id = 0, $dataFilter = '') {
		parent::__construct($atDataConnection, $dataFrom, $id, $dataFilter);

		$this->addProperty('addDTS', 'add_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00');
		$this->addProperty('addSourceTypeID', 'add_source_type_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('addSourceID', 'add_source_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('recordStatusID', 'record_status_id', self::DATA_TYPE_NUMBER, self::RECORD_STATUS_ACTIVE);
		$this->addProperty('recordStatusDTS', 'record_status_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00');
		$this->addProperty('recordStatusSourceTypeID', 'record_status_source_type_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('recordStatusSourceID', 'record_status_source_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('importDTS', 'import_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00');
		$this->addProperty('importSourceTypeID', 'import_source_type_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('importSourceID', 'import_source_id', self::DATA_TYPE_NUMBER, 0);
	}

	public function getRecordStatusName() {
		switch($this->recordStatusID) {
			case self::RECORD_STATUS_ACTIVE:
				return 'Active';
			case self::RECORD_STATUS_DELETED:
				return 'Deleted';
			case self::RECORD_STATUS_DISABLED:
				return 'Disabled';
		}
	}

	public function isActive() {
		return $this->recordStatusID == self::RECORD_STATUS_ACTIVE;
	}

	public function isDisabled() {
		return $this->recordStatusID == self::RECORD_STATUS_DISABLED;
	}

	public function isDeleted() {
		return $this->recordStatusID == self::RECORD_STATUS_DELETED;
	}

	#---- Flag the Data Record as Deleted
	public function delete($sql = '') {
		$return = true;

		$this->recordStatusID = self::RECORD_STATUS_DELETED;
		if (!$this->isNew()) {
			$return = $this->save();
		}

		return $return;
	}


	#---- Flag the Data Record as Disabled
	public function disable() {
		$return = true;

		$this->recordStatusID = self::RECORD_STATUS_DISABLED;
		if (!$this->isNew()) {
			$return = $this->save();
		}

		return $return;
	}


	#---- Flag the Data Record as Active
	public function enable() {
		$return = true;

		$this->recordStatusID = self::RECORD_STATUS_ACTIVE;
		if (!$this->isNew()) {
			$return = $this->save();
		}

		return $return;
	}


	public function save($sql = '', $id_sql = '') {
		global $phpAnvil;

		if ($this->isNew() && $this->addSourceID == 0) {
			$this->addDTS = date('Y-m-d H:i:s');
			$this->addSourceTypeID = SOURCE_TYPE_USER;
			$this->addSourceID = $phpAnvil->application->user->id;
		}

		return parent::save($sql, $id_sql);
	}


	#---- Flag the Data Record as Active
	public function unDelete() {
		return $this->enable();
	}

}


?>