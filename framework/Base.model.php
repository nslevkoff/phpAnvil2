<?php

require_once PHPANVIL2_COMPONENT_PATH . 'anvilForm/anvilFormObject.abstract.php';

abstract class BaseModel extends anvilFormObjectAbstract
{

    public function __construct(
        $anvilDataConnection,
        $dataFrom,
        $dataFilter = '')
    {
        global $phpAnvil;

        parent::__construct($anvilDataConnection, $phpAnvil->regional, $phpAnvil->modelDictionary, $dataFrom, $dataFilter);
    }

}


?>