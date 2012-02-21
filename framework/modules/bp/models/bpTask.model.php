<?php
/**
* @file
* BP Tasks Table Model
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup        BP_Module BP_Models
*
*/

require_once(PHPANVIL2_FRAMEWORK_PATH . 'RecordStatus.model.php');

/**
* Database table structure model for BP Tasks.
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup        BP_Module BP_Models
*/

class bpTaskModel extends RecordStatusModel
{

//    const GENDER_MALE = 1;
//    const GENDER_FEMALE = 2;


    public $bpTaskDTS;
    public $bpStatusID;
    public $processStartDTS;
    public $processEndDTS;
    public $processDuration;
    public $sourceAccountID;
    public $sourceModule;
    public $sourceReferenceNumber;
    public $targetAccountID;
    public $targetModule;
    public $targetReferenceNumber;
    public $targetCallback;
    public $data;
    public $recurTypeID;
    public $recurQty;
    public $recurEndDTS;


    public function __construct($anvilDataConnection, $id = 0)
    {
        unset($this->bpTaskDTS);
        unset($this->bpStatusID);
        unset($this->processStartDTS);
        unset($this->processEndDTS);
        unset($this->processDuration);
        unset($this->sourceAccountID);
        unset($this->sourceModule);
        unset($this->sourceReferenceNumber);
        unset($this->targetAccountID);
        unset($this->targetModule);
        unset($this->targetReferenceNumber);
        unset($this->targetCallback);
        unset($this->data);
        unset($this->recurTypeID);
        unset($this->recurQty);
        unset($this->recurEndDTS);


        $this->addProperty('id', 'bp_task_id', self::DATA_TYPE_NUMBER, 0);
        $this->addProperty('bpTaskDTS', 'bp_task_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'bpTaskDTS');
        $this->addProperty('bpStatusID', 'bp_status_id', self::DATA_TYPE_NUMBER, 0, 'bpStatusID');
        $this->addProperty('processStartDTS', 'process_start_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'processStartDTS');
        $this->addProperty('processEndDTS', 'process_end_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'processEndDTS');
        $this->addProperty('processDuration', 'process_duration', self::DATA_TYPE_NUMBER, 0,'processDuration');
        $this->addProperty('sourceAccountID', 'source_account_id', self::DATA_TYPE_NUMBER, 0,'sourceAccountID');
        $this->addProperty('sourceModule', 'source_module', self::DATA_TYPE_STRING, '','sourceModule');
        $this->addProperty('sourceReferenceNumber', 'source_reference_number', self::DATA_TYPE_NUMBER, 0,' sourceReferenceNumber');
        $this->addProperty('targetAccountID', 'target_account_id', self::DATA_TYPE_NUMBER, 0,'targetAccountID');
        $this->addProperty('targetModule', 'target_module', self::DATA_TYPE_STRING, '','targetModule');
        $this->addProperty('targetReferenceNumber', 'target_reference_number', self::DATA_TYPE_NUMBER, 0,' targetReferenceNumber');
        $this->addProperty('targetCallback', 'target_callback', self::DATA_TYPE_STRING, '','targetCallback');
        $this->addProperty('data', 'data', self::DATA_TYPE_STRING, '','data');
        $this->addProperty('recurTypeID', 'recur_type_id', self::DATA_TYPE_NUMBER, 0,'recurTypeID');
        $this->addProperty('recurQty', 'recur_qty', self::DATA_TYPE_NUMBER, 0,'recurQty');
        $this->addProperty('recurEndDTS', 'recur_end_dts', self::DATA_TYPE_DTS, '0000-00-00 00:00:00', 'recurEndDTS');


        parent::__construct($anvilDataConnection, SQL_TABLE_BP_TASKS, $id, '');

    }

}

?>
