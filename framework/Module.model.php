<?php

require_once('Base.model.php');

class ModuleModel extends BaseModel {

	const TYPE_FRAMEWORK = 1;
	const TYPE_CUSTOM = 2;

	public function __construct($anvilDataConnection, $id = 0) {
		$this->addProperty('id', 'module_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('moduleTypeID', 'module_type_id', self::DATA_TYPE_NUMBER, self::TYPE_CUSTOM);
		$this->addProperty('code', 'code', self::DATA_TYPE_STRING, '');
		$this->addProperty('name', 'name', self::DATA_TYPE_STRING, '');
		$this->addProperty('version', 'version', self::DATA_TYPE_STRING, '');

		parent::__construct($anvilDataConnection, SQL_TABLE_MODULES, $id, '');
	}

	public function loadCode($code) {
		$sql = 'SELECT * FROM ' . $this->dataFrom;
		$sql .= ' WHERE ' . $this->_dataFields['code'] . '=' . $this->_dataConnection->dbString($code);
		$sql .= ' LIMIT 0, 1';

		return $this->load($sql);
	}
}


?>