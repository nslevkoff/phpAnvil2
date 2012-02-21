<?php
/**
 * @file
 * Phrases Database Table Model
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Phrases_Module phpAnvil_Models
 *
 */

//require_once(APP_PATH . 'app/RecordStatus.model.php');
require_once(PHPANVIL2_FRAMEWORK_PATH . 'RecordStatus2.model.php');

/**
 * Database table structure model for the Phrases table.
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Phrases_Module phpAnvil_Models
 */

class PhraseModel extends RecordStatusModel2
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
     * @var string $phrase
     */

    public $phrase;


    public function __construct($anvilDataConnection, $id = 0)
    {
        unset($this->id);
        unset($this->constant);
        unset($this->name);
        unset($this->phrase);

//        $this->addProperty('id', 'phrase_id', self::DATA_TYPE_NUMBER, 0);
//        $this->addProperty('constant', 'constant', self::DATA_TYPE_STRING, '', 'constant');
//        $this->addProperty('name', 'name', self::DATA_TYPE_STRING, '', 'name');
//        $this->addProperty('phrase', 'phrase', self::DATA_TYPE_STRING, '', 'phrase');

        $this->addProperty('id', SQL_TABLE_I18N_PHRASES, 'dictionary_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('code', SQL_TABLE_I18N_PHRASES, 'code', self::DATA_TYPE_STRING);
        $this->addProperty('name', SQL_TABLE_I18N_PHRASES, 'name', self::DATA_TYPE_STRING);
        $this->addProperty('phrase', SQL_TABLE_I18N_PHRASES, 'phrase', self::DATA_TYPE_STRING);

//        parent::__construct($anvilDataConnection, SQL_TABLE_I18N_PHRASES, $id, '');
        parent::__construct($anvilDataConnection, SQL_TABLE_I18N_PHRASES);
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