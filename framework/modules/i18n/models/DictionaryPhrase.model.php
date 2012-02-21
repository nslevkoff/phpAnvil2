<?php
/**
 * @file
 * Dictionary Phrases Database Table Model
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dictionary_Phrases_Module phpAnvil_Models
 *
 */
//require_once(PHPANVIL2_FRAMEWORK_PATH . 'Base.model.php');
require_once(PHPANVIL2_FRAMEWORK_PATH . 'RecordStatus2.model.php');

/**
 * Database table structure model for the Dictionary Phrases table.
 *
 * @author        Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright    (c) 2010 Solutions By Design
 * @ingroup        Dictionary_Phrases_Module phpAnvil_Models
 */

class DictionaryPhraseModel extends RecordStatusModel2
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
     * @var int $dictionaryID
     */

    public $dictionaryID;

    /**
     *
     *
     * @var $phraseID
     */

    public $phraseID;


    public function __construct($anvilDataConnection, $id = 0)
    {
        unset($this->id);
        unset($this->dictionaryID);
        unset($this->phraseID);

        $this->addProperty('id', 'dictionary_phrase_id', self::DATA_TYPE_NUMBER, 0);
        $this->addProperty('dictionaryID', 'dictionary_id', self::DATA_TYPE_NUMBER, '', 'dictionaryID');
        $this->addProperty('phraseID', 'phrase_id', self::DATA_TYPE_NUMBER, '', 'phraseID');

        $this->addProperty('id', SQL_TABLE_I18N_DICTIONARY_PHRASES, 'dictionary_phrase_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('dictionaryID', SQL_TABLE_I18N_DICTIONARY_PHRASES, 'dictionary_id', self::DATA_TYPE_NUMBER);
        $this->addProperty('phraseID', SQL_TABLE_I18N_DICTIONARY_PHRASES, 'phrase_id', self::DATA_TYPE_NUMBER);

        parent::__construct($anvilDataConnection, SQL_TABLE_I18N_DICTIONARY_PHRASES, $id, '');
        parent::__construct($anvilDataConnection, SQL_TABLE_I18N_PHRASES);
        $this->id = $id;
    }

}

?>