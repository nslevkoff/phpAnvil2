<?php
require_once PHPANVIL2_FRAMEWORK_PATH . 'activity.model.php';

require_once 'anvilModel.abstract.php';

/**
 * @property string $addDTS
 * @property int    $addSourceTypeID
 * @property int    $addSourceID
 * @property int    $recordStatusID
 * @property string $recordStatusDTS
 * @property int    $recordStatusSourceTypeID
 * @property int    $recordStatusSourceID
 * @property string $importDTS
 * @property int    $importSourceTypeID
 * @property int    $importSourceID
 */
abstract class anvilRSModelAbstract extends anvilModelAbstract
{
    const RECORD_STATUS_SETUP    = 1;
    const RECORD_STATUS_ACTIVE   = 2;
    const RECORD_STATUS_DISABLED = 3;
    const RECORD_STATUS_DELETED  = 4;

    private $_rsName = array(
        'Unknown',
        'Setup',
        'Active',
        'Disabled',
        'Deleted'
    );

    public $recordStatusNames = array(
        'Unknown',
        'Setup',
        'Active',
        'Disabled',
        'Deleted'
    );

    const SOURCE_TYPE_UNKNOWN = 1;
    const SOURCE_TYPE_USER = 2;
    const SOURCE_TYPE_SYSTEM = 3;
    const SOURCE_TYPE_AJAX = 4;
    const SOURCE_TYPE_BP = 5;
    const SOURCE_TYPE_API = 6;
    const SOURCE_TYPE_GENERATED = 7;
    const SOURCE_TYPE_EMAIL = 8;
    const SOURCE_TYPE_IMPORT = 9;

    public $sourceTypeNames = array(
        'Unknown',
        'Unknown',
        'User',
        'System',
        'Ajax',
        'BP',
        'API',
        'Generated',
        'Email',
        'Import'
    );

    protected $_saveActivity = true;
    public $activityDescription = '';
    public $activityDetail = '';
    public $activityTypeIDOverride = 0;


    public function __construct($primaryTableName = '', $primaryFieldName = 'id', $formName = '')
    {
        parent::__construct($primaryTableName, $primaryFieldName, $formName);

//        $this->enableLog();


        $this->fields->addDTS->fieldName = 'add_dts';
        $this->fields->addDTS->fieldType = anvilModelField::DATA_TYPE_DTS;
        $this->fields->addDTS->activity = false;

        $this->fields->addSourceTypeID->fieldName = 'add_source_type_id';
        $this->fields->addSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->fields->addSourceTypeID->activity = false;

        $this->fields->addSourceID->fieldName = 'add_source_id';
        $this->fields->addSourceID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->fields->addSourceID->activity = false;

        $this->fields->recordStatusID->fieldName = 'record_status_id';
        $this->fields->recordStatusID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->fields->recordStatusID->valueNameArray = $this->recordStatusNames;
        $this->recordStatusID                    = self::RECORD_STATUS_ACTIVE;

        $this->fields->recordStatusDTS->fieldName = 'record_status_dts';
        $this->fields->recordStatusDTS->fieldType = anvilModelField::DATA_TYPE_DTS;
//        $this->fields->recordStatusDTS->activity = false;

        $this->fields->recordStatusSourceTypeID->fieldName = 'record_status_source_type_id';
        $this->fields->recordStatusSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->fields->recordStatusSourceTypeID->valueNameArray = $this->sourceTypeNames;

        $this->fields->recordStatusSourceID->fieldName = 'record_status_source_id';
        $this->fields->recordStatusSourceID->fieldType = anvilModelField::DATA_TYPE_NUMBER;

        $this->fields->importDTS->fieldName = 'import_dts';
        $this->fields->importDTS->fieldType = anvilModelField::DATA_TYPE_DTS;

        $this->fields->importSourceTypeID->fieldName = 'import_source_type_id';
        $this->fields->importSourceTypeID->fieldType = anvilModelField::DATA_TYPE_NUMBER;
        $this->fields->importSourceTypeID->valueNameArray = $this->sourceTypeNames;

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


    public function isSetup()
    {
        return $this->recordStatusID == self::RECORD_STATUS_SETUP;
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


    public function disableActivity()
    {
        $this->_saveActivity = false;
    }


    #---- Flag the Data Record as Active
    public function enable()
    {
        return $this->setRecordStatus(self::RECORD_STATUS_ACTIVE);
    }


    public function enableActivity()
    {
        $this->_saveActivity = true;
    }


    public function save($sql = '', $id_sql = '')
    {
        global $phpAnvil;

        $isChanged = true;
        $return    = false;

        $now = new DateTime(null, $phpAnvil->regional->dateTimeZone);

//		if ($this->isNew() && $this->addSourceID == 0) {
        if ($this->isNew()) {
//			$this->addDTS = date('Y-m-d H:i:s');
            $this->addDTS = $now->format($phpAnvil->regional->dtsFormat);

            if (empty($this->addSourceID)) {
                $this->addSourceTypeID = $phpAnvil->sourceTypeID;
                $this->addSourceID     = $phpAnvil->application->user->id;
            }

            if (empty($this->recordStatusID)) {
                $this->recordStatusID = self::RECORD_STATUS_ACTIVE;
            }
        } else {
            $isChanged = $this->isChanged();
        }

        if ($isChanged) {
//        if ($this->fields->field('recordStatusID')->changed)
            if ($this->fields->recordStatusID->changed) {
                $this->recordStatusDTS          = $now->format($phpAnvil->regional->dtsFormat);
                $this->recordStatusSourceTypeID = $phpAnvil->sourceTypeID;
                $this->recordStatusSourceID     = $phpAnvil->application->user->id;

            }

            if ($this->_saveActivity) {
                $activity                  = new ActivityModel();
                $activity->accountID       = $this->_core->application->account->id;
                $activity->targetTableName = $this->primaryTableName;
                $activity->activityTypeID  = ActivityModel::TYPE_UPDATED;

                $activity->description = $this->activityDescription;

                //---- Save Activity
                if ($this->activityTypeIDOverride) {
                    $activity->activityTypeID = $this->activityTypeIDOverride;
                    $activity->detail         = $this->activityDetail;
                } elseif ($this->isNew()) {
                    $activity->activityTypeID = ActivityModel::TYPE_ADDED;
                } else {

                    //---- Get Changed Field Array ---------------------------------

                    $changedArray = $this->fields->getChangedActivityArray();

//                    $this->enableLog();
//                    $this->_logError($changedArray, '$changedArray');

                    $activity->detail = json_encode($changedArray);

                    if ($this->fields->recordStatusID->changed) {
                        switch ($this->recordStatusID) {
                            case self::RECORD_STATUS_ACTIVE:
                                $activity->activityTypeID = ActivityModel::TYPE_ENABLED;
                                break;
                            case self::RECORD_STATUS_DISABLED:
                                $activity->activityTypeID = ActivityModel::TYPE_DISABLED;
                                break;
                            case self::RECORD_STATUS_DELETED:
                                $activity->activityTypeID = ActivityModel::TYPE_DELETED;
                                break;
                        }
                    }
                }

                $return = parent::save($sql, $id_sql);

                $activity->targetID = $this->id;
                $activity->save();

                $activity->__destruct();
                unset($activity);

            } else {
                $return = parent::save($sql, $id_sql);
            }

        } else {
            $this->_logVerbose('No fields have changed, skipping save...', $this->primaryTableName);
        }

        return $return;
    }


    #---- Flag the Data Record as Active
    public function unDelete()
    {
        return $this->enable();
    }

}


?>