<?php
require_once('anvilControl.abstract.php');


/**
* Page Control
*
* @copyright 	Copyright (c) 2010-2011 Nick Slevkoff ({@link http://www.slevkoff.com})
* @license		http://www.phpanvil.com/LICENSE.txt		New BSD License
* @version		1.0
 * @ingroup     phpAnvilTools
*/


class anvilPageNav extends anvilControlAbstract
{

//    const NAME                = 'anvilPageNav';
//    const VERSION            = '3.0';
//    const VERSION_BUILD     = '8';
//    const VERSION_DTS        = '4/6/2009 8:45:00 PM PST';
//    const COPYRIGHT            = 'Copyright (c) 2009 by Devuture, Inc.';


    public $qsPrefix = 'pn_';

    public $useDIV = true;

    public $maxNavPages = 5;

//    public $htmlID = 'pageNav';

//    public $mainClass;

//    public $maxRows = 25;
//    public $maxNavPages = 7;

    public $currentPage = 1;
    public $totalPages = 1;

    public $totalItems = 0;
    public $totalItemsName = 'Rows';

    public $itemsPerPage = 25;
    public $itemOffset = 0;
    public $webRootPath = '';

    public $imageNext = 'bPageNavNext.png';
    public $imagePrev = 'bPageNavPrev.png';
    public $imageFirst = 'bPageNavFirst.png';
    public $imageLast = 'bPageNavLast.png';
    public $imagePath = 'images/';

//    public $phraseNext = '&gt;';
//    public $phrasePrev = '&lt;';
//    public $phraseFirst = '«';
//    public $phraseLast = '»';

    public $phraseNext = 'Next&nbsp;»';
    public $phrasePrev = '«&nbsp;Previous';
    public $phraseFirst = 'First';
    public $phraseLast = 'Last';


    public function __construct($id = 'pageNav', $class = 'pageNav', $qsPrefix = 'pn_', $properties = null)
    {
//        $this->_traceEnabled = $traceEnabled;

        $this->enableLog();

        parent::__construct($id, $properties);

        $this->class = $class;
        $this->qsPrefix = $qsPrefix;

        $this->imagePath = $this->getBasePath() . '/images/';

        #---- Auto Detect Current Page
        $this->currentPage = isset($_GET[$this->qsPrefix . 'pg']) ? $_GET[$this->qsPrefix . 'pg'] : 1;
        if ((int)$this->currentPage < 2) {
            $this->itemOffset = 0;
            $this->currentPage = 1;
        } else {
            $this->itemOffset = ((int)$this->currentPage - 1) * (int)$this->itemsPerPage;
        }

    }


    public function __set($propertyName, $value)
    {
        global $firePHP;

        $return = '';

        $return = parent::__set($propertyName, $value);

        switch ($propertyName) {
            case 'totalItems':
            case 'totalPages':
            case 'itemsPerPage':

                $this->totalPages = ceil((int)$this->totalItems / (int)$this->itemsPerPage);
//                fb::log('$this->totalPages = ' . $this->totalPages);

            case 'itemOffset':
            case 'currentPage':

                if ((int)$this->currentPage < 2) {
                    $this->itemOffset = 0;
                    $this->currentPage = 1;
                } else {
                    $this->itemOffset = ((int)$this->currentPage - 1) * (int)$this->itemsPerPage;
                }

//                fb::log('$this->itemOffset = ' . $this->itemOffset);

                break;
        }

        return $return;
    }


    public function renderHTML($devTemplate = null)
    {
        return $this->render($devTemplate);
    }


    public function render($devTemplate = null)
    {
        global $phpAnvil;

//        fb::log($this->qsPrefix, '$this->qsPrefix');

//        global $firePHP;

//        $startTime = microtime(true);
//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Executing...');

//        fb::log($this);

        #---- Auto-Detect anvilPageNav Variables
//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Auto-Detecting QueryString Variables...');
/*
        $queryString = '';

        foreach ($_GET as $param => $value)
        {
            switch ($param) {
                case $this->qsPrefix . 'pg':
//                    $this->currentPage = $value;

//                    if ((int)$this->currentPage < 2) {
//                        $this->itemOffset = 0;
//                        $this->currentPage = 1;
//                    } else {
//                        $this->itemOffset = ((int)$this->currentPage - 1) * (int)$this->itemsPerPage;
//                    }

//                    fb::log($param . ' = ' . $value);
//                    fb::log('$this->itemOffset = ' . $this->itemOffset);

                    break;

                case $phpAnvil->qsModule:
                case $phpAnvil->qsAction:
                    break;

                default:
                    $queryString .= '&' . $param . '=' . $value;
            }
        }

        if (!empty($queryString))
        {
            $queryString = substr($queryString, 1);
        }
*/

        #----- Build URL Strings
//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Building URL Strings...');
//        $baseURL = $phpAnvil->site->webPath . $_GET[$phpAnvil->qsModule] . '/' . $_GET[$phpAnvil->qsAction] . '?' . $queryString;
        $baseURL = $this->getPagePath();

//        $currentPageURL = '';
//        if (!empty($page)) {
//            $currentPageURL = '&' . $this->statePrefix . 'pg=' . $page;
//        }


//        if ($this->useDIV) {
//            $html = '<div id="' . $this->htmlID . '" class="' . $this->mainClass . '">';
//        } else {
//            $html = '<table id="' . $this->htmlID . '" class="' . $this->mainClass . '" width="100%"><tr><td>';
//        }

//        if ($rows > 0) {
//            $maxRecordsPerPage = $rows;
//        } else {
//            $maxRecordsPerPage = $this->maxRows;
//            $rows = $maxRecordsPerPage;
//        }

        #---- Get Total Record Rows
//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Getting Total Records...');

//        if (isset($this->countSQL)) {
//            $objRS = $this->devData->execute($this->countSQL);
//        } else {
//            $objRS = $this->devData->execute($this->baseSQL);
//        }
//        if (!$objRS->hasRows()) {
//            if ($this->noRecordsMsgEnabled) {
//                $html .= '<div class="noData">No records available.</div>';
//            }
//        } else {

//            if (isset($this->countSQL)) {
//                $objRS->Read();
//                $totalRows = $objRS->data('total_rows');
//            } else {
//                $totalRows = $objRS->count();
//            }
//            $objRS->close();

//            #---- Calculate Total Pages
//            $totalPages = ceil($totalRows / $maxRecordsPerPage);

//            #---- Calculate Rows if Last Page
//            if ($page == $totalPages && $page != 1) {
//                $rows = $totalRows - (($page - 1) * $rows);
//            }


        $this->totalPages = ceil((int)$this->totalItems / (int)$this->itemsPerPage);

        if ((int)$this->currentPage < 2) {
            $this->itemOffset  = 0;
            $this->currentPage = 1;
        } else {
            $this->itemOffset = ((int)$this->currentPage - 1) * (int)$this->itemsPerPage;
        }

        #---- Render Page Navigation HTML
//            $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, 'Rendering Page Navigation HTML...');
            $pagesPerSide = ($this->maxNavPages - 1) / 2;

            $this->_logDebug($pagesPerSide, '$pagesPerSide');

//            fb::log($pagesPerSide, '$pagesPerSide');

//            if ($this->useDIV) {
                $html = '<div id="' . $this->id . '" class="pagination';
                if (!empty($this->class)) {
                    $html .= ' ' . $this->class;
                }
                $html .= '"><ul>';
//            } else {
//                $html = '<table id="' . $this->id . '" class="' . $this->class . '" width="100%"><tr>';
//            }

//            if ($this->useDIV) {
//                $pageNavHTML = '<div class="pageNav"><ul>';
//            } else {
//                $pageNavHTML = '<table class="pageNav"><tr>';
//            }


            $this->_logDebug($this->totalPages, '$this->totalPages');


            if ($this->totalPages <= 1) {
//                if ($this->useDIV) {
                    $html .= '<li class="pages"><a href="#">' . $this->totalItems . '&nbsp;' . $this->totalItemsName . '</a></li>';
//                } else {
//                    $html .= '<td class="pages" width="100%">' . $this->totalItems . ' ' . $this->totalItemsName . '</td>';
//                }
            } else {
//                if ($this->useDIV) {
                    $html .= '<li class="pages"><a href="#">' . $this->totalItems . '&nbsp;' . $this->totalItemsName . '&nbsp;in&nbsp;' . $this->totalPages . ' Pages:</a></li>';
//                } else {
//                    $html .= '<td class="pages" width="100%">' . $this->totalItems . '&nbsp;' . $this->totalItemsName . '&nbsp;in&nbsp;' . $this->totalPages . '&nbsp;Pages:</td>';
//                }

                if ($this->totalPages >= ($this->maxNavPages * 2)) {
                    if ($this->currentPage == 1) {
//                        if ($this->useDIV) {
//                            $html .= '<li class="disabled">' . $this->phraseFirst . '</li>';
//                        } else {
//                            $html .= '<td class="disabled">' . $this->phraseFirst . '</td>';
//                        }
                    } else {
                        $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', 1);
//                        if ($this->useDIV) {
                            $html .= '<li class="first"><a class="btn btn-small" href="' . htmlentities($url) . '">' . $this->phraseFirst . '</a></li>';
//                        } else {
//                            $html .= '<td class="first"><a href="' . htmlentities($url) . '">' . $this->phraseFirst . '</a></td>';
//                        }
                    }
                }

                if ($this->currentPage == 1) {
//                    if ($this->useDIV) {
//                        $html .= '<li class="disabled">' . $this->phrasePrev . '</li>';
//                    } else {
//                        $html .= '<td class="disabled">' . $this->phrasePrev . '</td>';
//                    }
                } else {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage-1);
//                    if ($this->useDIV) {
                        $html .= '<li class="prev"><a class="btn btn-small" href="' . htmlentities($url) . '" class="nextPrev">' . $this->phrasePrev . '</a></li>';
//                    } else {
//                        $html .= '<td class="prev"><a href="' . htmlentities($url) . '" class="nextPrev">' . $this->phrasePrev . '</a></td>';
//                    }
                }

                if (($this->currentPage - 1000) > 1) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage - 1000);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">' . ($this->currentPage - 1000) . '...</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">' . ($this->currentPage - 1000) . '...</a></td>';
//                    }
                }

                if (($this->currentPage - 100) > 1) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage - 100);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">' . ($this->currentPage - 100) . '...</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">' . ($this->currentPage - 100) . '...</a></td>';
//                    }
                }

                if (($this->currentPage - 10) > 1) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage - 10);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">' . ($this->currentPage - 10) . '...</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">' . ($this->currentPage - 10) . '...</a></td>';
//                    }
                }


                $firstNavPage = $this->currentPage - $pagesPerSide;
                if ($firstNavPage < 1) {
                    $firstNavPage = 1;
                }

                if ($this->totalPages > $this->maxNavPages) {
                    $totalNavPages = $firstNavPage + ($this->maxNavPages - 1);

                    if ($totalNavPages > $this->totalPages) {
                        $totalNavPages = $this->totalPages;
                        $firstNavPage = $totalNavPages - ($this->maxNavPages - 1);
                    }
                } else {
                    $totalNavPages = $this->totalPages;
                    $firstNavPage = 1;
                }


//                fb::log($firstNavPage, '$firstNavPage');
//                fb::log($totalNavPages, '$totalNavPages');

                for ($i=$firstNavPage; $i<=$totalNavPages; $i++) {
                    if ($i == $this->currentPage) {
//                        if ($this->useDIV) {
                            $html .= '<li class="active"><a class="btn btn-small active" href="#">' . $i . '</a></li>';
//                        } else {
//                            $html .= '<td class="selected">' . $i . '</td>';
//                        }
                    } else {
                        $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $i);
//                        if ($this->useDIV) {
//                            $html .= '<li><a href="' . htmlentities($baseURL . '&' . $this->qsPrefix . 'pg=' . $i) . '"';
                            $html .= '<li><a href="' . htmlentities($url) . '"';
//                        } else {
//                            $html .= '<td><a href="' . htmlentities($baseURL . '&' . $this->qsPrefix . 'pg=' . $i) . '"';
//                            $html .= '<td><a href="' . htmlentities($url) . '"';
//                        }

                        $html .= ' class="btn btn-small';
                        if ($i == $this->currentPage) {
                            $html .= ' active';
//                        } else {
//                            $html .= ' class="btn btn-small"';
                        }
                        $html .= '">' . $i . '</a>';
//                        if ($this->useDIV) {
                            $html .= '</li>';
//                        } else {
//                            $html .= '</td>';
//                        }
                    }
                }

                if (($this->currentPage + 10) < $this->totalPages) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage + 10);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">...' . ($this->currentPage + 10) . '</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">...' . ($this->currentPage + 10) . '</a></td>';
//                    }
                }

                if (($this->currentPage + 100) < $this->totalPages) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage + 100);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">...' . ($this->currentPage + 100) . '</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">...' . ($this->currentPage + 100) . '</a></td>';
//                    }
                }

                if (($this->currentPage + 1000) < $this->totalPages) {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage + 1000);
//                    if ($this->useDIV) {
                        $html .= '<li><a class="btn btn-small" href="' . htmlentities($url) . '">...' . ($this->currentPage + 1000) . '</a></li>';
//                    } else {
//                        $html .= '<td><a href="' . htmlentities($url) . '">...' . ($this->currentPage + 1000) . '</a></td>';
//                    }
                }

                if ($this->currentPage == $this->totalPages) {
//                    if ($this->useDIV) {
//                        $html .= '<li class="disabled">' . $this->phraseNext . '</li>';
//                    } else {
//                        $html .= '<td class="disabled">' . $this->phraseNext . '</td>';
//                    }
                } else {
                    $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->currentPage+1);
//                    if ($this->useDIV) {
                        $html .= '<li class="next"><a class="btn btn-small" href="' . htmlentities($url) . '">' . $this->phraseNext . '</a></li>';
//                    } else {
//                        $html .= '<td class="next"><a href="' . htmlentities($url) . '">' . $this->phraseNext . '</a></td>';
//                    }
                }

                if ($this->totalPages >= ($this->maxNavPages * 2)) {
                    if ($this->currentPage == $this->totalPages) {
//                        if ($this->useDIV) {
//                            $html .= '<li class="disabled">' . $this->phraseFirst . '</li>';
//                        } else {
//                            $html .= '<td class="disabled">' . $this->phraseFirst . '</td>';
//                        }
                    } else {
                        $url = $this->addQSVar($baseURL, $this->qsPrefix . 'pg', $this->totalPages);
//                        if ($this->useDIV) {
                            $html .= '<li class="last"><a class="btn btn-small" href="' . htmlentities($url) . '">' . $this->phraseLast . '</a></li>';
//                        } else {
//                            $html .= '<td class="last"><a href="' . htmlentities($url) . '">' . $this->phraseLast . '</a></td>';
//                        }
                    }
                }
            }
//            if ($this->useDIV) {
                $html .= '</ul></div>';
//            } else {
//                $html .= '</tr></table>';
//            }


//            $html = $pageNavHTML;

//        $currentTime = microtime(true);
//        $elapsedTime = number_format(($currentTime - $startTime) * 100, 2);

//        $this->_addTraceInfo(__FILE__, __METHOD__, __LINE__, '... done (' . $elapsedTime . ' ms)');

        return $html;

    }

    function addQSVar($url, $key, $value)
    {
//        $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
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
//        $this->_logDebug('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', 'RegEx');
        
        $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        return $url;
    }


    public function getBasePath()
    {
        $path = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
                ? 'https://' : 'http://';
        $path .= $_SERVER["SERVER_NAME"];
        if ($_SERVER["SERVER_PORT"] != '80' && $_SERVER["SERVER_PORT"] != '443') {
            $path .= ':' . $_SERVER["SERVER_PORT"];
        }

        return $path;
    }

    public function getPagePath()
    {
        $pagePath = $this->getBasePath();
        $pagePath .= $_SERVER["REQUEST_URI"];

//        $this->_logDebug($pagePath, '$pagePath 1');
//        $this->_logDebug($this->qsPrefix . 'pg', 'QS');
        
        $pagePath = $this->removeQSVar($pagePath, $this->qsPrefix . 'pg');
//        $this->_logDebug($pagePath, '$pagePath 2');

        return $pagePath;
    }

}




?>