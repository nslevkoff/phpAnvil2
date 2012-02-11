<?php
/**
 * @file
 * Dictionary Database Table Model
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dictionary_Module phpAnvil_Models
 *
 */


//require_once(APP_PATH . 'app/RecordStatus.model.php');
require_once(PHPANVIL_FRAMEWORK_PATH . 'RecordStatus2.model.php');

/**
 * Database table structure model for the Dictionary table.
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dictionary_Module phpAnvil_Models
 */

class DictionaryModel extends RecordStatusModel2
{

    /**
     *
     *
     * @var int $id
     */

    public $id;

    /**
     *
     *
     * @var string $constant
     */

    public $constant;
    /**
     *
     *
     * @var string $name
     */

    public $name;
    /**
     *
     *
     * @var string $filename
     */

    public $filename;


    public function __construct($atDataConnection, $id = 0)
    {
        unset($this->id);
        unset($this->constant);
        unset($this->name);
        unset($this->filename);

        $this->addProperty('id', SQL_TABLE_I18N_DICTIONARIES, 'dictionary_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('code', SQL_TABLE_I18N_DICTIONARIES, 'code', self::DATA_TYPE_STRING);
        $this->addProperty('name', SQL_TABLE_I18N_DICTIONARIES, 'name', self::DATA_TYPE_STRING);

//        $this->addProperty('id', 'dictionary_id', self::DATA_TYPE_NUMBER, 0);
//        $this->addProperty('constant', 'constant', self::DATA_TYPE_STRING, '', 'constant');
//        $this->addProperty('name', 'name', self::DATA_TYPE_STRING, '', 'name');
//        $this->addProperty('filename', 'filename', self::DATA_TYPE_STRING, '', 'filename');

//        parent::__construct($atDataConnection, SQL_TABLE_I18N_DICTIONARIES, $id, '');
        parent::__construct($atDataConnection, SQL_TABLE_I18N_DICTIONARIES);
        $this->id = $id;
    }


    public function save($sql = '', $id_sql = '')
    {
        $this->constant = preg_replace('/[^a-zA-Z0-9_\s]/', '', $this->constant);
        $this->constant = strtoupper(str_replace(' ', '_', $this->constant));
        return parent::save($sql, $id_sql);
    }

}

?>