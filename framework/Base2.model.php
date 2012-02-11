<?php

require_once(PHPANVIL_TOOLS_PATH . 'atFormObject2.abstract.php');

abstract class BaseModel2 extends atFormObjectAbstract2
{

    public function __construct(
        $atDataConnection,
        $dataFrom,
        $dataFilter = '')
    {
        global $phpAnvil;

        parent::__construct($atDataConnection, $phpAnvil->regional, $phpAnvil->modelDictionary, $dataFrom, $dataFilter);
    }

}


?>