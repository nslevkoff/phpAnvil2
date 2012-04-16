<?php
/**
 * @see anvilControlAbstract
 * @see anvilCollection
 */
require_once('anvilControl.abstract.php');
require_once('anvilCollection.class.php');
//require_once('anvilForm.class.php');
//require_once('anvilComboBox.class.php');
//require_once('anvilEntry.class.php');
//require_once('anvilButton.class.php');

$anvilGridCounter = 0;

/**
 * Grid Control
 *
 * @copyright   Copyright (c) 2009-2011 Nick Slevkoff (http://www.phpanvil.com)
 * @license     BSD License
 *              Full copyright and license information is available in the LICENSE.txt
 *              file that was distributed with this source file or can be found online
 *              at http://www.phpanvil.com/LICENSE.txt
 * @version     1.8
 * @ingroup     phpAnvilTools
 */
class anvilGrid extends anvilControlAbstract
{

    const VERSION = '1.8';


    #---- Column Format Constants
    const COLUMN_FORMAT_NONE    = 0;
    const COLUMN_FORMAT_PERCENT = 1;
    const COLUMN_FORMAT_MONEY   = 2;
    const COLUMN_FORMAT_NUMBER  = 3;
    const COLUMN_FORMAT_DATE    = 4;
    const COLUMN_FORMAT_DTS     = 5;

    #---- Calculation Constants
    const CALC_DIVIDE_PERCENT = 1;
    const CALC_DIVIDE_MONEY   = 2;
    const CALC_SUBTRACT       = 3;

    #---- Total Type Constants
    const TOTAL_TYPE_COUNT = 1;
    const TOTAL_TYPE_SUM   = 2;

    public $striped = false;
    public $bordered = false;
    public $condensed = true;


    public $statePrefix = 'ag';

    public $db;
    public $baseSQL;
    public $countSQL;

    public $gridRS;

    public $defaultOrderBy;
    public $defaultOrderByDesc = false;

    public $useDIV = true;

    public $htmlID = 'anvilGrid';

    public $headerEnabled = true;

    public $pageNavHeaderEnabled = true;
    public $pageNavFooterEnabled = false;

    public $mainClass;

    public $rowAltColorEnabled = true;

    public $columns;

    public $columnCalcEnabled = false;
    public $columnTotalEnabled = false;

    public $imageSortAsc = 'bGridSortAsc.gif';
    public $imageSortDesc = 'bGridSortDesc.gif';
    public $imagePath = 'images/';

    private $_dataEngine;

//    public $rowRenderBeginCallback;
//    public $rowRenderEndCallback;

    public $rowClass;
    public $rowData;

    public $rowURL;
    public $rowURLKeyColumn;

    public $dateFormat = '%m/%d/%Y';
    public $dtsFormat = '%m/%d/%Y %I:%M:%S';

    public $noRecordsMsg = 'No records available.';
    public $noRecordsMsgEnabled = true;

    public $recordStatusEnabled = true;

    public $anvilPageNav;

    public $filterRowEnabled = false;

    public $rowOffset = 0;
    public $maxRows = 25;
    public $baseURL;
    public $baseQueryString;
    public $rowHoverClass = 'rowHover';


    public function __construct(
        $anvilDataConnection = null,
        $sql = '',
        $id = 'anvilGrid',
        $mainClass = 'anvilGrid',
        $properties = null,
        $traceEnabled = false)
    {
        global $anvilGridCounter;

                $this->enableLog();


        //        $this->addProperty('rowOffset', 0);
        //        $this->addProperty('maxRows', 25);
        //        $this->addProperty('baseURL', '');
        //        $this->addProperty('baseQueryString', '');
        //        $this->addProperty('rowHoverClass', 'rowHover');

        $this->columns = new anvilGridColumns(true);

        $this->db      = $anvilDataConnection;
        $this->baseSQL = $sql;
        $this->htmlID  = $id;

        //        $this->statePrefix = $this->htmlID . '_';

        $this->mainClass = $mainClass;

        $this->baseURL   = $this->getPagePath();
        $this->imagePath = $this->getBasePath() . '/images/';
        $anvilGridCounter++;
        $this->statePrefix .= $anvilGridCounter . '_';

        if (is_object($anvilDataConnection)) {
            $this->_dataEngine = constant(get_class($anvilDataConnection) . '::ENGINE');
        }

        parent::__construct($id, $properties, $traceEnabled);

        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '... done (' . date('h:i:s A') . ')');
    }


    protected function _processTokens($value, $data, $delimiterLeft = '{{$', $delimiterRight = '}}')
    {
        $return = $value;

//        $this->_logDebug($value, '$value');
//        $this->_logDebug($data, '$data');
        foreach ($data as $dataKey => $dataValue) {
            if (!is_numeric($dataKey)) {
                $return = str_ireplace($delimiterLeft . $dataKey . $delimiterRight, $dataValue, $return);
            }
        }

        return $return;
    }

    public function setOrderBy($orderBy, $isDescending = false)
    {
        $this->defaultOrderBy     = $orderBy;
        $this->defaultOrderByDesc = $isDescending;
    }


    public function renderHTML($anvilTemplate = null, $page = 1, $rows = 0)
    {
        return $this->render($anvilTemplate, $page, $rows);
    }


    public function renderColumnStyle($columnOptions)
    {
        $html = '';
        if ($columnOptions && (!empty($columnOptions->justify) || !empty($columnOptions->style))) {
            $html .= ' style="';
            if (!empty($columnOptions->justify)) {
                $html .= 'text-align:' . $columnOptions->justify . ';';
            }
            if (!empty($columnOptions->style)) {
                $html .= $columnOptions->style;
            }
            $html .= '"';
        }

        return $html;
    }


    function addQSVar($url, $key, $value)
    {
        //        $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        //        $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', '$1$2$4', $url . '&');
        //        $url = substr($url, 0, -1);
        $url = $this->removeQSVar($url, $key);
        if (strpos($url, '?') === false) {
            return ($url . '?' . $key . '=' . $value);
        } else {
            return ($url . '&' . $key . '=' . $value);
        }
    }


    function removeQSVar($url, $key)
    {
        //        $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        return $url;
    }


    public function getBasePath()
    {
        $path = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
                ? 'https://'
                : 'http://';
        $path .= $_SERVER["SERVER_NAME"];
        if ($_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443') {
            $path .= ':' . $_SERVER["SERVER_PORT"];
        }

        return $path;
    }


    public function getPagePath()
    {
        $pagePath = $this->getBasePath();
        //        $pagePath = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
        //                ? 'https://' : 'http://';
        //        $pagePath .= $_SERVER["SERVER_NAME"];
        //        if ($_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443') {
        //            $pagePath .= ':' . $_SERVER["SERVER_PORT"];
        //        }
        $pagePath .= $_SERVER["REQUEST_URI"];

        $pagePath = $this->removeQSVar($pagePath, $this->statePrefix . 'o');
        $pagePath = $this->removeQSVar($pagePath, $this->statePrefix . 'oc');

        return $pagePath;
    }


    public function render($anvilTemplate = null, $page = 1, $rows = 0)
    {
        //        global $firePHP;

        $html      = '';
        $startTime = microtime(true);
        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');

        if (isset($this->anvilPageNav)) {
            $this->rowOffset = $this->anvilPageNav->itemOffset;
            $this->maxRows   = $this->anvilPageNav->itemsPerPage;

            if ($this->pageNavHeaderEnabled || $this->pageNavFooterEnabled) {
                $pageNavHTML = $this->anvilPageNav->render();
            }

            //---- Render anvilPageNav Header -------------------------------------
            if ($this->pageNavHeaderEnabled) {
                $html .= $pageNavHTML;
            }
        }

        $orderBy   = $this->defaultOrderBy;
        $orderDesc = $this->defaultOrderByDesc;

        if ($orderDesc) {
            $orderByDirection = ' DESC';
        } else {
            $orderByDirection = ' ASC';
        }


        //---- Auto-Detect anvilGrid Variables ------------------------------------
        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Auto-Detecting anvilGrid QueryString Variables...');
        $queryString = $this->baseQueryString;

        //        fb::Log($queryString, '$queryString');

        foreach ($_GET as $param => $value) {
            switch ($param) {
                case $this->statePrefix . 'o':
                    $orderDesc = $value == 'd';
                    if ($value == 'd') {
                        $orderByDirection = ' DESC';
                    } else {
                        $orderByDirection = ' ASC';
                    }
                    break;
                case $this->statePrefix . 'oc':
                    $orderBy = $value;
                    break;
                //                case $this->statePrefix . 'pg':
                //                    $page = $value;
                //                    break;
                //                default:
                //                    $queryString .= '&' . $param . '=' . $value;
            }
        }

        //        $firePHP->_log('$queryString = ' . $queryString);
        //        fb::Log($queryString, '$queryString');

        if (!empty($queryString)) {
            $queryString = substr($queryString, 1);
        }

        //        $firePHP->_log('$queryString = ' . $queryString);
        //        fb::Log($queryString, '$queryString');

        //----- Build URL Strings ----------------------------------------------
        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Building URL Strings...');

        //        $this->pagePath = $phpAnvil->site->webPath . ltrim($_SERVER['REDIRECT_URL'], '/');
        //        $this->_logDebug($this->pagePath, 'pagePath');

        //        $baseURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?" . $queryString;
        //        $baseURL = $this->baseURL . '?' . $queryString;

        $currentOrderURL = '';
        if (!empty($orderBy)) {
            $currentOrderURL = '&' . $this->statePrefix . 'oc=' . $orderBy;
            if ($orderDesc) {
                $currentOrderURL .= '&' . $this->statePrefix . 'o=d';
            } else {
                $currentOrderURL .= '&' . $this->statePrefix . 'o=a';
            }
        }
        $currentPageURL = '';
        //        if (!empty($page)) {
        //            $currentPageURL = '&' . $this->statePrefix . 'pg=' . $page;
        //        }


        //---- disabled in v2 --------------------------------------------------
        //        if ($this->useDIV) {
        //            $html .= '<div id="' . $this->htmlID . '" class="' . $this->mainClass . '">';
        //        } else {
        //            $html .= '<table id="' . $this->htmlID . '" class="' . $this->mainClass . '" width="100%"><tr><td>';
        //        }
        //----------------------------------------------------------------------


        #---- Get Limited Records for a Particular Page
        $sql = $this->baseSQL;
        if (!empty($orderBy)) {
            $sql .= ' ORDER BY ' . $orderBy . $orderByDirection;
        }

        if ($this->_dataEngine == 'mysql' || $this->_dataEngine == 'mysqli') {
            $sql .= ' LIMIT ' . $this->rowOffset . ',' . $this->maxRows;
        }

//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, $sql);
        //            $firePHP->_log($sql);
        //            fb::Log($sql, '$sql');
//        $this->_logDebug($sql, 'anvilGrid SQL');

        if (isset($this->gridRS)) {
            $objRS = $this->gridRS;
            if (isset($orderBy)) {
                $objRS->sort($orderBy, $orderDesc);
            }
        } else {
            $objRS = $this->db->execute($sql);
        }


        #---- If MSSQL, Forward to Grid First Row
        //            if ($this->_dataEngine == 'mssql') {
        //                $objRS->moveToRow((($page-1) * $maxRecordsPerPage));

        //            }


        if ($objRS->read()) {

            $html .= '<table';

            //---- ID ----------------------------------------------------------
            if ($this->id) {
                $html .= ' id="' . $this->id . '"';
            }

            //---- Class -------------------------------------------------------
            $html .= ' class="table';

            if ($this->striped) {
                $html .= ' table-striped';
            }

            if ($this->bordered) {
                $html .= ' table-bordered';
            }

            if ($this->condensed) {
                $html .= ' table-condensed';
            }
            $html .= '">';

            $columnCount = $objRS->columns->count();

            //                fb::log($objRS, '$objRS');


            #----------------------------------------------
            #---- Render Column Header
            #----------------------------------------------

            if ($this->headerEnabled) {
                $html .= '<thead><tr>';
                for ($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext())
                {
                    $objColumn = $objRS->columns->current();

                    //                        $firePHP->_log($objColumn);

                    //                        if (!array_key_exists($objColumn->name, $this->columnVisible) || $this->columnVisible[$objColumn->name]) {
                    $columnOptions = $this->columns->column($objColumn->name);

                    //                        $firePHP->_log($columnOptions);

                    if (!$columnOptions || $columnOptions->visible) {

                        //                            if (array_key_exists($objColumn->name, $this->_columnHeaderDisabled)) {
                        if ($columnOptions && !$columnOptions->headerEnabled) {
                            $html .= '<th class="disabled" />';
                        } else {
                            #---- Determine Column Title
                            //                                if (array_key_exists($objColumn->name, $this->columnTitle)) {
                            if ($columnOptions && !empty($columnOptions->title)) {
                                //                                    $columnTitle = $this->columnTitle[$objColumn->name];
                                $columnTitle = $columnOptions->title;
                            } else {
                                $columnTitle = ucwords(str_replace('_', ' ', $objColumn->name));
                            }

                            $html .= '<th';

                            if (!$columnOptions || ($columnOptions && $columnOptions->sortable)) {
                                if ($objColumn->name == $orderBy) {
                                    $html .= ' class="selected"';
                                } else {
                                    $html .= " class='sortable' onmouseover=\"$(this).toggleClass('colHover', true);\" onmouseout=\"$(this).toggleClass('colHover', false);\"";
                                }
                            }

                            //---- Render Column Style ---------------------
                            //                                if ($columnOptions && (!empty($columnOptions->justify) || !empty($columnOptions->style)))
                            //                                {
                            //                                    $html .= ' style="';
                            //                                    if (!empty($columnOptions->justify))
                            //                                    {
                            //                                        $html .= 'text-align:' . $columnOptions->justify . ';';
                            //                                    }
                            //                                    if (!empty($columnOptions->style))
                            //                                    {
                            //                                        $html .= $columnOptions->style;
                            //                                    }
                            //                                    $html .= '"';
                            //                                }
                            $html .= $this->renderColumnStyle($columnOptions);

                            //                            $html .= '><nobr>';
                            $html .= '>';

                            #---- Column Sort Order Link

                            if (!$columnOptions || ($columnOptions && $columnOptions->sortable)) {
                                $url = $this->addQSVar($this->baseURL, $this->statePrefix . 'oc', $objColumn->name);

                                //                                $html .= '<a href="' . htmlentities($baseURL . $currentPageURL);
                                //                                $html .= htmlentities('&' . $this->statePrefix . 'oc=' . $objColumn->name);
                                if ($objColumn->name == $orderBy) {
                                    if ($orderDesc) {
                                        //                                        $html .= htmlentities('&' . $this->statePrefix . 'o=a');
                                        $url = $this->addQSVar($url, $this->statePrefix . 'o', 'a');
                                    } else {
                                        //                                        $html .= htmlentities('&' . $this->statePrefix . 'o=d');
                                        $url = $this->addQSVar($url, $this->statePrefix . 'o', 'd');
                                    }
                                }
                                $html .= '<a href="' . htmlentities($url) . '">';
                                //                                $html .= '">';
                            }

                            $html .= $columnTitle;

                            #---- Column Sort Order Image

                            if ($objColumn->name == $orderBy && (!$columnOptions || ($columnOptions && $columnOptions->sortable))) {
                                if ($orderDesc) {
                                    //                                    $html .= '&nbsp;<img alt="Descending Order" src="' . $this->imagePath . $this->imageSortDesc . '">';
                                    $html .= '&nbsp;<i class="icon-chevron-down"></i>';
                                } else {
                                    //                                    $html .= '&nbsp;<img alt="Ascending Order" src="' . $this->imagePath . $this->imageSortAsc . '">';
                                    $html .= '&nbsp;<i class="icon-chevron-up"></i>';
                                }
                            }

                            if (!$columnOptions || ($columnOptions && $columnOptions->sortable)) {
                                $html .= '</a>';
                            }
                            //                            $html .= '</nobr></th>';
                            $html .= '</th>';
                        }
                    }
                }
                $html .= '</tr></thead>';
            }


            #===================================================================-
            #---- Render Body
            #----------------------------------------------

            $isAltRow = true;
            $html .= '<tbody>';


            #---- Filter Row -----------------------------------------------
            if ($this->filterRowEnabled) {
                $html .= '<tr class="filterRow">';
                for ($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext())
                {
                    $objColumn = $objRS->columns->current();

                    $columnOptions = $this->columns->column($objColumn->name);

                    if (!$columnOptions || $columnOptions->visible) {
                        $entry = '';

                        if (isset($columnOptions->filterEntryCallback)) {
                            $entry = call_user_func($columnOptions->filterEntryCallback, $this, $objColumn->name);
                            //---- Refresh $columnOptions in case it changed from the custom callback
                            $columnOptions = $this->columns->column($objColumn->name);
                        }


                        $html .= '<td';
                        //                            if ($columnOptions && !empty($columnOptions->justify))
                        //                            {
                        //                                $html .= ' style="text-align:' . $columnOptions->justify . ';"';
                        //                            }

                        if ($objColumn->name == $orderBy) {
                            $html .= ' class="orderedCell"';
                        }

                        $html .= $this->renderColumnStyle($columnOptions);
                        $html .= '>';

                        $html .= $entry;

                        if (empty($entry)) {
                            //                            $entry = new anvilEntry('', $this->statePrefix . $objColumn->name, $columnOptions->filterEntrySize, 40, '');
                            //                            $html .= $entry->render();
                        }

                        //                            $html .= $columnTitle;

                        $html .= '</td>';
                    }
                }
                $html .= '</tr>';
            }


            //---- Grid Rows -----------------------------------------------
            do
            {


                $this->rowData = $objRS->getRowArray();


                #---- Alt Row Color Class
                if ($isAltRow && $this->rowAltColorEnabled) {
                    $this->rowClass = '';
                } else {
                    $this->rowClass = 'altRow';
                }


                $rowHoverClass = $this->rowHoverClass;


                //---- phpAnvil's Record Status Model Support --------------
                if ($this->recordStatusEnabled && isset($this->rowData['record_status_id'])) {
                    switch ($this->rowData['record_status_id'])
                    {
                        //---- Disabled
                        case anvilRSModelAbstract::RECORD_STATUS_DISABLED:
                            $this->rowClass .= ' disabled';
//                                $rowHoverClass .= ' disabled';
                            break;

                        //---- Deleted
                        case anvilRSModelAbstract::RECORD_STATUS_DELETED:
                            $this->rowClass .= ' deleted';
//                                $rowHoverClass .= ' deleted';
                            break;
                    }
                }

                //                    FB::log($this->rowClass, 'rowClass');


                #---- Execute Row Begin Callback
                //                $this->executeCallback('rowRenderBegin', $this);

                //                    if (!empty($this->rowRenderBeginCallback)) {
                //                        call_user_func($this->rowRenderBeginCallback, $this);
                //                    }


                //                    $html .= '<tr class="' . $this->rowClass . '" onmouseover="this.toggleClass(\'' . $rowHoverClass . '\', true);" onmouseout="this.className=\'' . $this->rowClass . '\';">';
                $html .= '<tr class="' . $this->rowClass . '" onmouseover="$(this).toggleClass(\'' . $rowHoverClass . '\', true);" onmouseout="$(this).toggleClass(\'' . $rowHoverClass . '\', false);">';
                $isAltRow = !$isAltRow;

                for ($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext()) {
                    $objColumn = $objRS->columns->current();

                    $columnOptions = $this->columns->column($objColumn->name);

                    //                        if (!array_key_exists($objColumn->name, $this->columnVisible) || $this->columnVisible[$objColumn->name]) {
                    if (!$columnOptions || $columnOptions->visible) {

                        #---- Column Callback Set?
                        if ($columnOptions && !empty($columnOptions->renderCallback)) {
                            $content = call_user_func($columnOptions->renderCallback, $objColumn->name, $this->rowData);

                            //                                $this->_logDebug($columnOptions, '$columnOptions #1');
                            //
                            //                                $columnOptions = $this->columns->column($objColumn->name);
                            //
                            //                                $this->_logDebug($columnOptions, '$columnOptions #2');

                            $content = $this->applyColumnCalc($this->rowData, $objColumn->name, $content);
                        } else {
                            $content = $this->applyColumnCalc($this->rowData, $objColumn->name, $objRS->data($objColumn->name));
                        }

                        $useColumnURL = false;
                        $useRowURL    = false;

                        //---- Use Column URL?
                        if ($columnOptions && !empty($columnOptions->url)) {
                            $useColumnURL = true;
                        } else {
                            //---- Use Row URL?
                            if ((!empty($this->rowURL) && !$columnOptions) || (!empty($this->rowURL) && $columnOptions && $columnOptions->rowClickable)) {
                                $useRowURL = true;
                            }
                        }


                        $html .= '<td class="';

                        //                            if (array_key_exists($objColumn->name, $this->_columnCustomClass)) {
                        if ($columnOptions && !empty($columnOptions->customClass)) {
                            //                                $html .= $this->_columnCustomClass[$objColumn->name];
                            $html .= $columnOptions->customClass;
                        } elseif ($objColumn->name == $orderBy) {
                            $html .= 'orderedCell';
                        } else {
                            $html .= 'cell';
                        }

                        //                        if (($columnOptions && !empty($columnOptions->url)) || (!empty($this->rowURL) && $columnOptions && $columnOptions->rowClickable)) {
                        if ($useColumnURL || $useRowURL) {
                            $html .= ' link';
                        } else {
                            $html .= ' pure';
                        }

                        $html .= '"';

                        //                            if (array_key_exists($objColumn->name, $this->_justifyColumn)) {
                        //                            if ($columnOptions && !empty($columnOptions->justify))
                        //                            {
                        //                                $html .= ' style="text-align:' . $this->_justifyColumn[$objColumn->name] . ';"';
                        //                                $html .= ' style="text-align:' . $columnOptions->justify . ';"';
                        //                            }
                        $html .= $this->renderColumnStyle($columnOptions);
                        $html .= '>';

                        #---- Is Column Linked? ----
                        /*
                                                    if (array_key_exists($objColumn->name, $this->_columnHref)) {
                                                        $html .= '<a href="' . htmlentities($this->_columnHref[$objColumn->name]);
                                                        if (array_key_exists($objColumn->name, $this->_columnHrefColumnName)) {
                                                            $html .= htmlentities($objRS->data($this->_columnHrefColumnName[$objColumn->name]));
                                                        }
                                                        $html .= '">';

                                                    }
                        */
                        if ($useColumnURL) {
                            $html .= '<a href="' . htmlentities($this->_processTokens($columnOptions->url, $this->rowData));
                            if (!empty($columnOptions->urlColumn)) {
                                $html .= htmlentities($this->rowData[$columnOptions->urlColumn]);
                            }
                            $html .= '">';
                            //                        } elseif (!empty($this->rowURL) && $columnOptions && $columnOptions->rowClickable) {
                        } elseif ($useRowURL) {
                            $html .= '<a href="' . htmlentities($this->_processTokens($this->rowURL, $this->rowData));
                            if (!empty($this->rowURLKeyColumn)) {
                                $html .= htmlentities($this->rowData[$this->rowURLKeyColumn]);
                            }
                            $html .= '">';

                            if (empty($content)) {
                                $html .= '&nbsp;';
                            }
                        }

                        $html .= $content;

                        #---- Is Column Linked? ----
                        //                            if (array_key_exists($objColumn->name, $this->_columnHref)) {
                        //                        if ($columnOptions && !empty($columnOptions->url)) {
                        //                        if (($columnOptions && !empty($columnOptions->url)) || !empty($this->rowURL)) {
                        //                        if (($columnOptions && !empty($columnOptions->url)) || (!empty($this->rowURL) && $columnOptions && $columnOptions->rowClickable)) {
                        if ($useColumnURL || $useRowURL) {
                            $html .= '</a>';
                        }

                        $html .= '</td>';

                        #---- COLUMN TOTALS ----
                        if ($this->columnTotalEnabled) {
                            //                                if (array_key_exists($objColumn->name, $this->_columnTotalType)) {
                            if ($columnOptions && !empty($columnOptions->totalType)) {
                                //                                    if(!array_key_exists($objColumn->name, $this->_columnTotal)){
                                //                                    if(empty($columnOptions->total)){
                                //                                        $this->_columnTotal[$objColumn->name] = 0;
                                //                                    }
                                //                                    switch ($this->_columnTotalType[$objColumn->name]) {
                                switch ($columnOptions->totalType) {
                                    case self::TOTAL_TYPE_COUNT:
//                                            $this->_columnTotal[$objColumn->name] += 1;
                                        $columnOptions->total += 1;
                                        break;
                                    case self::TOTAL_TYPE_SUM:
//                                            $this->_columnTotal[$objColumn->name] += $objRS->data($objRS->columns->getIndex());
                                        $columnOptions->total += $objRS->data($objRS->columns->getIndex());
                                        break;
                                }
                            }
                        }
                        #-----------------------

                    }
                }

                #---- Execute Row End Callback
                //                $this->executeCallback('rowRenderEnd', $this);

                $html .= '</tr>';
            } while ($objRS->read());

            $html .= '</tbody>';


            #----------------------------------------------
            #---- Render Column Footer
            #----------------------------------------------

            if ($this->columnTotalEnabled) {
                $html .= '<tfoot><tr class="footer">';
                for ($objRS->columns->moveFirst(); $objRS->columns->hasMore(); $objRS->columns->moveNext()) {
                    $objColumn     = $objRS->columns->current();
                    $columnOptions = $this->columns->column($objColumn->name);

                    //                        if (!array_key_exists($objColumn->name, $this->columnVisible) || $this->columnVisible[$objColumn->name]) {
                    if (!$columnOptions || $columnOptions->visible) {
                        #---- Column Header
                        $html .= '<td class="footer';
                        if ($objColumn->name == $orderBy) {
                            $html .= ' selected';
                        }
                        $html .= '"';

                        //                            if (array_key_exists($objColumn->name, $this->_justifyColumn)) {
                        //                            if ($columnOptions && $columnOptions->justify) {
                        //                                $html .= ' style="text-align:' . $this->_justifyColumn[$objColumn->name] . ';"';
                        //                                $html .= ' style="text-align:' . $columnOptions->justify . ';"';
                        //                            }
                        $html .= $this->renderColumnStyle($columnOptions);
                        $html .= '>';

                        #---- Column Contents
                        //                            if (array_key_exists($objColumn->name, $this->_columnTotalType)) {
                        if ($columnOptions && $columnOptions->totalType) {
                            //                                if (array_key_exists($objColumn->name, $this->_columnTotal)) {
                            //                                if ($columnOptions->total) {
                            //                                    $html .= $this->applyColumnFormat($objColumn->name, $this->_columnTotal[$objColumn->name]);
                            $html .= $this->applyColumnFormat($objColumn->name, $columnOptions->total);
                            //                                }
                        }

                        //                            } elseif (array_key_exists($objColumn->name, $this->_columnTotalCalcType)) {
                        if ($columnOptions && $columnOptions->totalCalcType) {
                            //                                if (array_key_exists($this->_columnTotalCalcField2[$objColumn->name], $this->_columnTotal)) {
                            if ($columnOptions->totalCalcField2) {

                                $calcColumn1 = $this->columns->column($columnOptions->totalCalcField1);
                                $calcColumn2 = $this->columns->column($columnOptions->totalCalcField2);

                                //                                    switch ($this->_columnTotalCalcType[$objColumn->name]) {
                                switch ($columnOptions->totalCalcType) {
                                    case self::CALC_DIVIDE_PERCENT:
//                                                if ($this->_columnTotal[$this->_columnTotalCalcField2[$objColumn->name]] == 0) {
                                        if ($calcColumn2->total == 0) {
                                            $html .= $this->applyColumnFormat($objColumn->name, '0');
                                        } else {
                                            //return number_format(($rowArray[$this->_columnCalcField1[$columnName]] / $rowArray[$this->_columnCalcField2[$columnName]]) * 100, 4);
                                            //                                                    $html .= $this->applyColumnFormat($objColumn->name, ($this->_columnTotal[$this->_columnTotalCalcField1[$objColumn->name]] / $this->_columnTotal[$this->_columnTotalCalcField2[$objColumn->name]]) * 100);
                                            $html .= $this->applyColumnFormat($objColumn->name, ($calcColumn1->total / $calcColumn2->total) * 100);
                                        }
                                        break;

                                    case self::CALC_DIVIDE_MONEY:
//                                            if ($this->_columnTotal[$this->_columnTotalCalcField2[$objColumn->name]] == 0) {
                                        if ($calcColumn2->total == 0) {
                                            $html .= $this->applyColumnFormat($objColumn->name, '0.00');
                                        } else {
                                            //return round(($this->col_total[$this->col_total_field1[$V6f6c99bb]] / $this->col_total[$this->col_total_field2[$V6f6c99bb]]), 2);

                                            //return number_format(($rowArray[$this->_columnCalcField1[$columnName]] / $rowArray[$this->_columnCalcField2[$columnName]]), 2);
                                            //                                                $html .= $this->applyColumnFormat($objColumn->name, ($this->_columnTotal[$this->_columnTotalCalcField1[$objColumn->name]] / $this->_columnTotal[$this->_columnTotalCalcField2[$objColumn->name]]));
                                            $html .= $this->applyColumnFormat($objColumn->name, ($calcColumn1->total / $calcColumn2->total));
                                        }
                                        break;

                                    case self::CALC_SUBTRACT:
//                                            if ($calcColumn2->total == 0) {
//                                                $html .= $this->applyColumnFormat($objColumn->name, '0.00');
//                                            } else {
                                        $html .= $this->applyColumnFormat($objColumn->name, ($calcColumn1->total - $calcColumn2->total));
//                                            }
                                        break;
                                }
                            }
                        }

                        #---- Column Footer
                        $html .= '</td>';
                    }
                }
                $html .= '</tr></tfoot>';
            }


            $html .= '</table>';

            #---- Add Page Navigation to Bottom of Grid
            //                if ($this->pageNavBottomEnabled && $totalPages > 1) {
            //                    $html .= $pageNavHTML;
            //                }

        } else {
            if ($this->noRecordsMsgEnabled && $this->noRecordsMsg != '') {
                $html .= '<div class="noData">' . $this->noRecordsMsg . '</div>';
            }
            //                if ($this->noRecordsMsgEnabled) {
            //                    $html .= '<div class="noData">No records available.</div>';
            //                }
        }
        //if (TRACE && $this->_isTraceEnabled) DevTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, '$objRS->close();');
        $objRS->close();
        //        }

        //---- disabled in v2 --------------------------------------------------
        //        if ($this->useDIV) {
        //            $html .= '</div>';
        //        } else {
        //            $html .= '</td></tr></table>';
        //        }
        //----------------------------------------------------------------------


        //---- Render anvilPageNav Footer -----------------------------------------
        if (isset($this->anvilPageNav) && $this->pageNavFooterEnabled) {
            $html .= $pageNavHTML;
        }


        $currentTime = microtime(true);
        $elapsedTime = number_format(($currentTime - $startTime) * 100, 2);

        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '... done (' . $elapsedTime . ' ms)');

        return $html;

    }


    public function setColumnLink($columnName, $href, $hrefColumnName = '')
    {
        $this->_columnHref[$columnName] = $href;
        if ($hrefColumnName != '') {
            $this->_columnHrefColumnName[$columnName] = $hrefColumnName;
        }
    }


    public function disableColumnHeader($columnName)
    {
        $this->_columnHeaderDisabled[$columnName] = true;
    }


    public function disableNoRecordsMsg()
    {
        $this->_noRecordsMsgEnabled = false;
    }


    public function enableNoRecordsMsg()
    {
        $this->_noRecordsMsgEnabled = true;
    }


    public function setColumnCustomClass($columnName, $class)
    {
        $this->_columnCustomClass[$columnName] = $class;
    }


    public function justifyColumn($columnName, $alignment)
    {
        $this->_justifyColumn[$columnName] = $alignment;
    }


    public function onColumnRender($columnName, $callback)
    {
        $this->_columnRenderCallback[$columnName] = $callback;
    }


    public function onRowRenderBegin($callback)
    {
        //        $this->addCallback('onRowRenderBegin', $callback);
        $this->addCallback('rowRenderBegin', $callback);
        //        $this->_rowRenderBeginCallback = $callback;
    }


    public function onRowRenderEnd($callback)
    {
        //        $this->addCallback('onRowRenderEnd', $callback);
        $this->addCallback('rowRenderEnd', $callback);
        //        $this->_rowRenderEndCallback = $callback;
    }


    public function setColumnFormat($columnName, $formanvilType, $decimals = 0)
    {
        $this->_columnFormat[$columnName]            = $formanvilType;
        $this->_columnFormanvilDecimals[$columnName] = $decimals;

        if ($formanvilType != self::COLUMN_FORMAT_NONE) {
            $this->justifyColumn($columnName, 'right');
        }
    }


    private function applyColumnFormat($columnName, $columnData)
    {
        //if (TRACE && $this->_isTraceEnabled) DevTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, date('h:i:s A'));

        $columnOptions = $this->columns->column($columnName);

        $newData = $columnData;
        //        if (isset($this->_columnFormat[$columnName])) {
        if ($columnOptions && !empty($columnOptions->format)) {
            //            switch ($this->_columnFormat[$columnName]) {
            switch ($columnOptions->format) {
                case self::COLUMN_FORMAT_PERCENT:
//                    $newData = number_format($columnData, $this->_columnFormanvilDecimals[$columnName]) . '%';
                    $newData = number_format($columnData, $columnOptions->formanvilDecimals) . '%';
                    break;
                case self::COLUMN_FORMAT_MONEY:
//                    $newData = '$' . number_format($columnData, $this->_columnFormanvilDecimals[$columnName]);
                    $newData = '$' . number_format($columnData, $columnOptions->formanvilDecimals);
                    break;
                case self::COLUMN_FORMAT_NUMBER:
//                    $newData = number_format($columnData, $this->_columnFormanvilDecimals[$columnName]);
                    $newData = number_format($columnData, $columnOptions->formanvilDecimals);
                    break;
                case self::COLUMN_FORMAT_DATE:
                    $newData = strftime($this->dateFormat, strtotime($columnData));
                    break;
                case self::COLUMN_FORMAT_DTS:
                    $newData = strftime($this->dtsFormat, strtotime($columnData));
                    break;
            }
        }

        //if (TRACE && $this->_isTraceEnabled) DevTrap::addTraceInfo(__FILE__, __METHOD__, __LINE__, date('h:i:s A'));

        return $newData;
    }


    public function setColumnCalc($columnName, $field1, $calcType, $field2)
    {

        $columnOptions = $this->columns->column($columnName, true);

        $columnOptions->calcField1 = $field1;
        $columnOptions->calcType   = $calcType;
        $columnOptions->calcField2 = $field2;

        $return = true;

        return $return;
    }


    function applyColumnCalc($rowArray, $columnName, $columnData)
    {
        //        fb::log($rowArray, '$rowArray');
        //        fb::log($columnName, '$columnName');
        //        fb::log($columnData, '$columnData');

        if ($this->columnCalcEnabled) {
            $columnOptions = $this->columns->column($columnName);

            //            if(isset($this->_columnCalcType[$columnName])){
            if ($columnOptions && !empty($columnOptions->calcType)) {
                #-- process calculation and return result

                //                switch ($this->_columnCalcType[$columnName]) {
                switch ($columnOptions->calcType) {
                    case self::CALC_DIVIDE_PERCENT:
//                            if ($rowArray[$this->_columnCalcField2[$columnName]] == 0) {
                        if ($rowArray[$columnOptions->calcField2] == 0) {
                            return $this->applyColumnFormat($columnName, '0');
                        } else {
                            //                                return $this->applyColumnFormat($columnName, ($rowArray[$this->_columnCalcField1[$columnName]] / $rowArray[$this->_columnCalcField2[$columnName]]) * 100);
                            return $this->applyColumnFormat($columnName, ($rowArray[$columnOptions->calcField1] / $rowArray[$columnOptions->calcField2]) * 100);
                        }
                        break;

                    case self::CALC_DIVIDE_MONEY:
//                        if ($rowArray[$this->_columnCalcField2[$columnName]] == 0) {
                        if ($rowArray[$columnOptions->calcField2] == 0) {
                            return $this->applyColumnFormat($columnName, '0.00');
                        } else {
                            //                            return $this->applyColumnFormat($columnName, ($rowArray[$this->_columnCalcField1[$columnName]] / $rowArray[$this->_columnCalcField2[$columnName]]));
                            return $this->applyColumnFormat($columnName, ($rowArray[$columnOptions->calcField1] / $rowArray[$columnOptions->calcField2]));
                        }
                        break;

                    case self::CALC_SUBTRACT:
//                        if ($rowArray[$columnOptions->calcField2] == 0) {
//                            return $this->applyColumnFormat($columnName, '0');
//                        } else {
                        return $this->applyColumnFormat($columnName, ($rowArray[$columnOptions->calcField1] - $rowArray[$columnOptions->calcField2]));
//                        }
                        break;
                }

            }
        }
        return $this->applyColumnFormat($columnName, $columnData);
    }
}


/**
 * Collection Class of Columns for the Grid Control
 *
 * @copyright   Copyright (c) 2009-2011 Nick Slevkoff (http://www.phpanvil.com)
 * @license     BSD License
 *              Full copyright and license information is available in the LICENSE.txt
 *              file that was distributed with this source file or can be found online
 *              at http://www.phpanvil.com/LICENSE.txt
 * @ingroup     anvilGrid
 */
class anvilGridColumns
{

    private $_columns = array();


//    public function __construct($config = array())
//    {
    // Allow accessing properties as either array keys or object properties:
//        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
//    }


/**
 * @param string $name
 * @param bool $addIfNotExist
 * @return anvilGridColumn
 */
    public function column($name, $addIfNotExist = false)
    {
        //        global $firePHP;

        $name = strtolower($name);

        //        $firePHP->_log($this->_columns);

        if (array_key_exists($name, $this->_columns)) {
            //            $firePHP->_log('Column Found!');
            $return = $this->_columns[$name];
        } else {
            //            $firePHP->_log('Column NOT Found!');
            if ($addIfNotExist) {
                $this->_columns[$name] = new anvilGridColumn();
                $return                = $this->_columns[$name];
            } else {
                $return = false;
            }
        }

        return $return;
    }


    public function __get($propertyName)
    {
        //        global $firePHP;

        $propertyName = strtolower($propertyName);

        //        $firePHP->_log('Getting column (' . $propertyName . ')...');

        if (!array_key_exists($propertyName, $this->_columns)) {
            $this->_columns[$propertyName] = new anvilGridColumn();
            //                $this->add(new DevGrid_Column(), $propertyName);
        }

        $return = $this->_columns[$propertyName];

        return $return;
    }


    public function __isset($propertyName)
    {
        //        global $firePHP;

        $propertyName = strtolower($propertyName);

        //        $firePHP->_log('Is column (' . $propertyName . ') Set?');

        $return = array_key_exists($propertyName, $this->_columns);

        return $return;

    }


    public function __set($propertyName, $value)
    {
        //        global $firePHP;

        $propertyName = strtolower($propertyName);

        //        $firePHP->_log('Setting column (' . $propertyName . ') to:');
        //        $firePHP->_log($value);
    }

}


/**
 * Column Property Class for the Grid Control
 *
 * @copyright   Copyright (c) 2009-2011 Nick Slevkoff (http://www.phpanvil.com)
 * @license     BSD License
 *              Full copyright and license information is available in the LICENSE.txt
 *              file that was distributed with this source file or can be found online
 *              at http://www.phpanvil.com/LICENSE.txt
 * @ingroup     anvilGrid
 */
class anvilGridColumn
{
    #---- Column Format Constants
    const FORMAT_NONE    = 0;
    const FORMAT_PERCENT = 1;
    const FORMAT_MONEY   = 2;
    const FORMAT_NUMBER  = 3;
    const FORMAT_DATE    = 4;
    const FORMAT_DTS     = 5;
    const FORMAT_EMAIL   = 6;

    public $title;
    public $visible = true;
    public $sortable = true;
    public $url;
    public $urlColumn;
    public $rowClickable = true;
    public $justify;
    public $renderCallback;
    public $format;
    public $formanvilDecimals;
    public $headerEnabled = true;
    public $customClass;
    public $css;
    public $style;
    public $calcType;
    public $calcField1;
    public $calcField2;
    public $total = 0;
    public $totalType;
    public $totalDecimals;
    public $totalCalcType;
    public $totalCalcField1;
    public $totalCalcField2;
    public $filterEntryEnabled = true;
    public $filterEntryCallback;
    public $filterEntrySize = 5;
}


?>
