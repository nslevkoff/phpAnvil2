<?php
/**
* @file
* Doxygen Module Controller
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @license
*     This source file is subject to the new BSD license that is
*     bundled with this package in the file LICENSE.txt. It is also
*     available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
* @ingroup      Doxygen_Module
*
*/

require_once(PHPANVIL_FRAMEWORK_PATH . 'Base.module.php');


/**
*
* Builds a string containing a Dot graph node.
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      Doxygen_Module
*/
function buildDotNode($name, $label, $url = '', $otherAttributes = '')
{
    $return = '    ';
    $return .= $name;
    $return .= ' [label="' . $label . '"';
    if (!empty($url))
    {
        $return .= ', URL="@ref ' . $url . '"';
    }
    if (!empty($otherAttributes))
    {
        $return .= ', ' . $otherAttributes;
    }
    $return .= '];' . "\n";

    return $return;
}

/**
*
* Builds a string containing a Dot graph relationship.
*
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      Doxygen_Module
*/
function buildDotRelationship($fromNode, $toNode, $label, $fromLabel = '', $toLabel = '', $otherAttributes = '')
{
    $return = '    ';
    $return .= $fromNode;
    $return .= ' -> ';
    $return .= $toNode;
    $return .= ' [';

    if (!empty($label))
    {
        $return .= ' label="' . $label . '"';
    }

    if (!empty($fromLabel))
    {
        $return .= ' taillabel="' . $fromLabel . '"';
    }

    if (!empty($toLabel))
    {
        $return .= ' headlabel="' . $toLabel . '"';
    }

    if (!empty($otherAttributes))
    {
        $return .= ' ' . $otherAttributes;
    }
    $return .= '];' . "\n";

    return $return;
}


/**
*
* Doxygen Module Class
*
* @version      v1.0.5
* @date         9/3/2010
* @author       Nick Slevkoff <nick@slevkoff.com>
* @copyright    Copyright (c) 2010 Nick Slevkoff (http://www.slevkoff.com)
* @ingroup      Doxygen_Module
*
*/
class DoxygenModule extends BaseModule {

	const NAME          = 'Doxygen Module';
	const CODE          = 'Doxygen';
	const VERSION       = '1.0';
	const VERSION_BUILD = '5';
	const VERSION_DTS   = '9/3/2010 11:30:00 AM PST';


    /**
    * A string containing the copyright for the page.
    */
    public $copyright = '';

    /**
    * A string containing a custom description.
    */
	public $description = '';

    public $dotDiagram = '';

    /**
    * A string containing the path to save the file.
    */
    public $filePath = '';

    /**
    * A string containing the page block.
    */
    public $page = '';

    /**
    * A string containing the groups the page will be in.
    */
    public $inGroups = '';

    /**
    * A string containing a custom @see block.
    */
	public $see = array();

    /**
    * An array of DB Table field names and notes.
    */
	public $notes = array();

    /**
    * A boolean indicating whether to include data. [FALSE]
    */
    public $includeData = false;


	function __construct()
	{
		global $phpAnvil;

		$this->enableTrace();
		$return = parent::__construct();

//		$phpAnvil->loadDictionary('Doxygen');

		return $return;
	}


    public function clear()
    {
        $this->copyright = '';
        $this->description = '';
        $this->filePath = '';
        $this->page = '';
        $this->inGroups = '';
        $this->see = array();
        $this->notes = array();
        $this->includeData = false;
    }


    public function createDBDictionaryFile(Action $action) {
        global $phpAnvil;
        global $firePHP;

        $return = true;

        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
//        $content .= '* @page ' . $this->page . "\n";
//        $content .= '* @page ' . $table . '_table ' . $table . ' Table' . "\n";
        $content .= '* @page db_dictionary Database Dictionary' . "\n";
        $content .= '*' . "\n";

        $sql = 'SHOW TABLES';
        $objRS = $phpAnvil->db->execute($sql);

        if ($objRS->count() > 0) {
            while ($objRS->read()) {
                $content .= '* - @subpage ' . $objRS->data(0) . "\n";
            }
            $objRS->close();
        }

        $content .= '*' . "\n";

        $content .= '*/' . "\n";
        $content .= '?>' . "\n";



        $filePath = APP_PATH . 'doxygen/page_db_dictionary.doc.php';

//        FB::log($filePath);
//        FB::log($content);

//        $this->addTraceInfo(__FILE__, __METHOD__, __LINE__, '$filePath = ' . $filePath, self::TRACE_TYPE_DEBUG);


        file_put_contents($filePath, $content);

        if ($action->source == SOURCE_TYPE_AJAX) {
            FB::log('Processing AJAX request...');

            $phpAnvil->actionMsg->add('Database Dictionary Doxygen file has been CREATED.');

//            $return = $phpAnvil->site->webPath . 'i18n/Dictionaries';
        }

        FB::log($return);

        return $return;
    }


	public function createDBTableFile(Action $action) {
		global $phpAnvil;
		global $firePHP;

//        FB::log($action);

		$return = true;

//        FB::log($action->data);

		$table = $action->data;

//        FB::log($table);

        $content2 = '';

		$content = '<?php' . "\n";
        $content .= '/**' . "\n";
//        $content .= '* @page ' . $this->page . "\n";
//        $content .= '* @page ' . $table . '_table ' . $table . ' Table' . "\n";
        $content .= '* @page ' . $table . ' ' . $table . ' Table' . "\n";
        $content .= '*' . "\n";

        if (!empty($this->description))
        {
            $content .= '* ' . $this->description . "\n";
            $content .= '*' . "\n";
        }


        if (!empty($this->dotDiagram))
        {
            $content .= '@dot' . "\n";
            $content .= $this->dotDiagram;
            $content .= '@enddot' . "\n";
            $content .= '*' . "\n";
        }

//        $content .= '* @copyright    ' . $this->copyright . "\n";
//        $content .= '* @ingroup      ' . $this->inGroups . "\n";
//        $content .= '*' . "\n";
        $content .= '* @section table_def Table Definition' . "\n";
        $content .= '*   <table class="docTable">' . "\n";
//        $content .= '*       <thead>' . "\n";
        $content .= '*       <tr class="header">' . "\n";
        $content .= '*           <td class="header">KEY</td>' . "\n";
        $content .= '*           <td class="header">Name</td>' . "\n";
        $content .= '*           <td class="header">Type</td>' . "\n";
//        $content .= '*           <td>Size</td>' . "\n";
        $content .= '*           <td class="header">NULL</td>' . "\n";
        $content .= '*           <td class="header">Default</td>' . "\n";
        $content .= '*           <td class="header">Notes</td>' . "\n";
        $content .= '*       </tr>' . "\n";
//        $content .= '*       </thead>' . "\n";
//        $content .= '*       <tbody>' . "\n";

        $sql = 'SHOW COLUMNS FROM ' . $table;
        $objRS = $phpAnvil->db->execute($sql);

        $rowClass = 'row';
        $maxColumnns = 0;

        if ($objRS->count() > 0) {
            while ($objRS->read()) {
                $maxColumnns++;

                $content .= '*       <tr class="' . $rowClass . '">' . "\n";
                $content .= '*           <td>' . $objRS->data('Key') . '</td>' . "\n";
                $content .= '*           <td>' . "\n";
                $content .= '*               @anchor ' . $objRS->data('Field') . "\n";
                $content .= '*               ' . $objRS->data('Field') . "\n";
                $content .= '*           </td>' . "\n";
                $content .= '*           <td>' . $objRS->data('Type') . '</td>' . "\n";
                $content .= '*           <td>' . $objRS->data('Null') . '</td>' . "\n";
                $content .= '*           <td>' . $objRS->data('Default') . '</td>' . "\n";

                //---- Notes
                $content .= '*           <td>';
                if ($objRS->data('Extra') <> '')
                {
                    $content .= '[' . $objRS->data('Extra') . '] ';
                }
                if (isset($this->notes[$objRS->data('Field')]) && $objRS->data('Key') <> 'PRI')
                {
                    $content .= $this->notes[$objRS->data('Field')];
                }
                $content .= '</td>' . "\n";
                $content .= '*       </tr>' . "\n";

                if ($rowClass === 'row')
                {
                    $rowClass = 'rowAlt';
                } else {
                    $rowClass = 'row';
                }

                if ($this->includeData)
                {
                    $content2 .= '*           <td class="header">' . $objRS->data('Field') . '</td>' . "\n";
                }

            }
            $objRS->close();
        }

//        $content .= '*       </tbody>' . "\n";
        $content .= '*   </table>' . "\n";

        if ($this->includeData)
        {
            $content .= '*' . "\n";
            $content .= '* @section table_data Table Data' . "\n";
            $content .= '*   <table class="docTable">' . "\n";
            $content .= '*       <tr class="header">' . "\n";
            $content .= $content2;
            $content .= '*       </tr>' . "\n";

            $sql = 'SELECT * FROM ' . $table;
            $objRS = $phpAnvil->db->execute($sql);

            $rowClass = 'row';

            if ($objRS->count() > 0) {
                while ($objRS->read()) {
                    $content .= '*       <tr class="' . $rowClass . '">' . "\n";

                    for ($i = 0; $i < $maxColumnns; $i++) {
                        $content .= '*           <td>' . $objRS->data($i) . '</td>' . "\n";
                    }

                    $content .= '*       </tr>' . "\n";

                    if ($rowClass === 'row')
                    {
                        $rowClass = 'rowAlt';
                    } else {
                        $rowClass = 'row';
                    }
                }
                $objRS->close();
            }

            $content .= '*   </table>' . "\n";
        }

        $content .= '*' . "\n";

        $count = count($this->see);
        for ($i = 0; $i < $count; $i++) {
            $content .= '* @see ' . $this->see[$i] . "\n";
        }

        $content .= '*/' . "\n";
        $content .= '?>' . "\n";



		$filePath = $this->filePath . 'page_' . $table . '_table.doc.php';

//		$this->addTraceInfo(__FILE__, __METHOD__, __LINE__, '$filePath = ' . $filePath, self::TRACE_TYPE_DEBUG);


		file_put_contents($filePath, $content);

		if ($action->source == SOURCE_TYPE_AJAX) {
			FB::log('Processing AJAX request...');

			$phpAnvil->actionMsg->add($table . ' table Doxygen file has been CREATED.');

//			$return = $phpAnvil->site->webPath . 'i18n/Dictionaries';
		}

		FB::log($return);

		return $return;
	}


	function processAction(Action $action)
	{
		global $phpAnvil;
		global $firePHP;

		$return = true;

		switch ($action->type) {

			case ACTION_CREATE_ALL_DOXYGEN_FILES:
                $sql = 'SELECT *';
                $sql .= ' FROM ' . SQL_TABLE_MODULES;
                $sql .= ' ORDER BY module_id';

                $objRS = $phpAnvil->db->execute($sql);

                while ($objRS->read()) {
                    $phpAnvil->loadModule($objRS->data('code'));
                }

                $phpAnvil->processNewAction($action->source, $phpAnvil->modules['*']->id, ACTION_BUILD_DOXYGEN_FILES, null);

                $return = $this->createDBDictionaryFile($action);

				break;

			case ACTION_CREATE_DB_TABLE_DOXYGEN_FILE:
				$return = $this->createDBTableFile($action);
				break;

		}
		return $return;
	}

}

?>