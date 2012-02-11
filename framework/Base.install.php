<?php
require_once(PHPANVIL_TOOLS_PATH . 'atObject.abstract.php');
require_once('Action.model.php');


class BaseInstaller extends atObjectAbstract {

	function __construct() {
		$this->enableTrace();

		return true;
	}


	function install(ModuleModel $objModule) {
        global $phpAnvil;
        global $moduleIDs, $moduleCodes, $moduleTypes;

		$this->registerInstaller($objModule);
		$objModule->save();

        $moduleConstant = 'MODULE_' . strtoupper($objModule->code);

        if (!defined($moduleConstant))
        {
            define($moduleConstant, $objModule->id);
        }

        $moduleIDs[strtolower($objModule->code)] = $objModule->id;
        $moduleCodes[$objModule->id] = $objModule->code;
        $moduleTypes[$objModule->id] = $objModule->moduleTypeID;

//		$this->deleteActions($objModule->id);
		$this->registerActions($objModule);

        $this->registerModule($objModule);
        $objModule->save();

        if ($phpAnvil->mode == phpAnvil::MODE_INSTALLING_MODULE)
        {
            $this->installDB();
        } else if ($phpAnvil->mode == phpAnvil::MODE_REINSTALLING_MODULE)
        {
            $this->reinstallDB();
        }

		return true;
	}


    function installDB()
    {
    }


    function reinstallDB()
    {
    }


    function uninstallDB()
    {
    }


	function deleteActions($moduleID) {
		global $phpAnvil;

		$sql = 'DELETE FROM ' . SQL_TABLE_ACTIONS;
		$sql .= ' WHERE owner_module_id = ' . $moduleID;

		$phpAnvil->db->execute($sql);

		return true;
	}


	function registerAction($objModule, $moduleID, $constant, $name) {
		global $phpAnvil, $actions;

		$newAction = new ActionModel($phpAnvil->db);
//		if (!$newAction->loadConstant($constant)) {
        $newAction->loadConstant($constant);
            $newAction->ownerModuleID = $objModule->id;
			$newAction->moduleID = $moduleID;
			$newAction->constant = $constant;
			$newAction->name = $name;
			$newAction->save();

            $actions[strtolower($objModule->code)][$constant] = $newAction->id;

            $actionConstant = 'ACTION_' . $newAction->constant;

            if (!defined($actionConstant))
            {
                define($actionConstant, $newAction->id);
            }
//		}

		return true;
	}


	function registerActions(ModuleModel $objModule) {
		return true;
	}


    function registerInstaller(ModuleModel $objModule) {
        return true;
    }

	function registerModule(ModuleModel $objModule) {
		return true;
	}

    function uninstall(ModuleModel $objModule)
    {
        $this->uninstallDB();
        $this->deleteActions($objModule->id);
        $this->unregisterModule($objModule);

        return true;
    }

    function unregisterModule(ModuleModel $objModule)
    {
        $objModule->delete();

        return true;
    }

}

?>