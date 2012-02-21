<?php

require_once('Base.model.php');

class ActionModel extends BaseModel {

	public function __construct($anvilDataConnection, $id = 0) {
		$this->addProperty('id', 'action_id', self::DATA_TYPE_NUMBER, 0);
        $this->addProperty('ownerModuleID', 'owner_module_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('moduleID', 'module_id', self::DATA_TYPE_NUMBER, 0);
		$this->addProperty('constant', 'constant', self::DATA_TYPE_STRING, '');
		$this->addProperty('name', 'name', self::DATA_TYPE_STRING, '');

		parent::__construct($anvilDataConnection, SQL_TABLE_ACTIONS, $id, '');
	}

	public function loadConstant($constant) {
		$sql = 'SELECT * FROM ' . $this->dataFrom;
		$sql .= ' WHERE ' . $this->_dataFields['constant'] . '=' . $this->_dataConnection->dbString($constant);
		$sql .= ' LIMIT 0, 1';

		return $this->load($sql);
	}
}


?>