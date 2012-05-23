<?php

require_once 'anvilController.abstract.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilAlert.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilResponse/anvilHTMLResponseHead.class.php';

/**
 * @property anvilContainer $response
 */
abstract class anvilHTMLControllerAbstract extends anvilControllerAbstract
{

    private $_breadcrumbTitle = array();
    private $_breadcrumbURL = array();
    public $breadcrumbDivider = '/';
//    public $breadcrumbLastDivider = '/';

    /**
     * @var anvilTemplateAbstract
     */
    protected $_template;

    /**
     * @var string
     */
    protected $_templateFilename;

    /**
     * @var anvilHTMLResponseHead
     */
    protected $_head;

    /**
     * @var array
     */
    public $page = array();

    private $_preClientScript;
    private $_postClientScript;

    protected $_tokenArray = array();

    protected $_webPath;


    function __construct()
    {
        parent::__construct();

        $this->enableLog();

        $this->_template = $this->_application->newTemplate();
        $this->_head   = new anvilHTMLResponseHead();

        $this->response = new anvilContainer();
//        $this->alerts = new anvilContainer();

        //---- Set Initial Tokens ----------------------------------------------
        $appTokens = array(
            'name'          => $this->_application->name,
            'refName'       => $this->_application->refName,
            'version'       => $this->_application->version,
            'build'         => $this->_application->build,
            'copyright'     => $this->_application->copyright,
            'copyrightHTML' => $this->_application->copyrightHTML
        );
        $this->_tokenArray['app'] = $appTokens;

        $this->_tokenArray['webPath'] = $this->_site->webPath;
//        $this->_webPath = $this->_site->webPath;

        return true;
	}


    function init()
    {
        $return = parent::init();

        return $return;
    }


    function open()
    {
        $return = parent::open();

        return $return;
    }


    protected function _addBreadcrumb($title, $url)
    {
        $this->_breadcrumbTitle[] = $title;
        $this->_breadcrumbURL[]   = $url;

        return true;
    }


    protected function _addControl($control)
    {
        $this->response->addControl($control);
    }


    protected function _assign($var, $value)
    {
        $this->_template->assign($var, $value);
    }


    protected function _assignTokens()
    {
        global $phpAnvil;

        $this->_head->render();
        $this->_head->html .= $this->_preClientScript;

        $this->_tokenArray['head'] = (array)$this->_head;
//        $this->_assign('head', (array)$this->_head);

//        $this->_assign('postClientScript', $this->_postClientScript);

//        $this->_assign('page', $this->page);

        //---- Prepare Breadcrumbs ---------------------------------------------
        $count = count($this->_breadcrumbTitle);

        $html = '';

        if ($count > 0) {
            $html .= '<ul class="breadcrumb">';

            for ($i = 0; $i < $count; $i++) {
                $html .= '<li>';

                if (!empty($this->_breadcrumbURL[$i])) {
                    if (strpos($this->_breadcrumbURL[$i], 'http') === false) {
                        $html .= '<a href="' . $phpAnvil->site->webPath . $this->_breadcrumbURL[$i] . '">';
                    } else {
                        $html .= '<a href="' . $this->_breadcrumbURL[$i] . '">';
                    }
                }
                $html .= $this->_breadcrumbTitle[$i];
                if (!empty($this->_breadcrumbURL[$i])) {
                    $html .= '</a>';
                }
                $html .= ' <span class="divider">' . $this->breadcrumbDivider . '</span>';
                $html .= '</li>';
            }
            $html .= '</ul>';
        }

//        $this->_assign('breadcrumbs', $html);
        $this->_tokenArray['page']['breadcrumbs'] = $html;


        //---- Assign Tokens to Template ---------------------------------------
        $this->_logDebug($this->_tokenArray, 'tokenArray');

        $tokenKeys = array_keys($this->_tokenArray);
        $count = count($tokenKeys);

//        $this->_logDebug($count, '$count');

        for ($i=0; $i < $count; $i++) {
            $this->_assign($tokenKeys[$i], $this->_tokenArray[$tokenKeys[$i]]);
        }
    }



    protected function _display()
    {
        global $phpAnvil;

//        $appTokens = array(
//            'name'          => $phpAnvil->application->name,
//            'refName'       => $phpAnvil->application->refName,
//            'version'       => $phpAnvil->application->version,
//            'build'         => $phpAnvil->application->build,
//            'copyright'     => $phpAnvil->application->copyright,
//            'copyrightHTML' => $phpAnvil->application->copyrightHTML
//        );
//        $this->assign('app', $appTokens);


        //---- HEAD ------------------------------------------------------------
//        $this->assign('webPath', $phpAnvil->site->webPath);

        $alerts = $this->_renderAlerts();
//        $this->_logDebug($alerts, '$alerts');
//        $this->_assign('alerts', $alerts);
        $this->_tokenArray['app']['alerts'] = $alerts;



        if (is_object($this->_template)) {
            $this->_template = clone $this->_template;
        }

        $this->_assignTokens();

        $this->_displayControls();

        return $this->_template->display($this->_templateFilename);

//        return $this->response->display();
    }


    protected function _displayControls()
    {
        //		$this->_logDebug('Executing...');

        //        fb::log('anvilPage.displayControls()');
        $this->_preClientScript  = $this->response->renderPreClientScript();
        $this->_postClientScript = $this->response->renderPostClientScript();

        $return = '';
        for ($this->response->controls->moveFirst(); $this->response->controls->hasMore(); $this->response->controls->moveNext()) {
            $objControl = $this->response->controls->current();
            //			$this->_logDebug('Display Control:id_' . $objControl->id);
            $this->response->preRenderControl($objControl);
            if ($this->_templateFilename && is_object($this->_template)) {

                $msg = 'Assign Control-Template:id_' . $objControl->id;
//                $this->_logDebug($msg);

                //        fb::log($msg);
                $html = $objControl->render($this->_template);

//                $this->_logDebug($html);

                $this->_assign('id_' . $objControl->id, $html);
            }
        }
        return $return;
    }


    private function _renderAlertType($type, $typeName)
    {
        $html = '';

        if (array_key_exists('alert.' . $typeName, $_SESSION)) {
            $messages = explode('|-[', $_SESSION['alert.' . $typeName]);

//            $this->_logDebug($messages, '$messages');

            $max = count($messages);

//            $this->_logDebug($max, '$max');

            for ($i=1; $i < $max; $i++) {
//                $this->_logDebug($i, '$i');

                $message = explode(']|', $messages[$i]);

//                $this->_logDebug($message, '$message');

                $objAlert = new anvilAlert('', $type, $message[0], $message[1]);
                $html .= $objAlert->renderContent();
            }

            $_SESSION['alert.' . $typeName] = '';

        }

        return $html;
    }



    protected function _renderAlerts()
    {
        $html = '';
        $html .= $this->_renderAlertType(anvilAlert::TYPE_ERROR, 'error');
        $html .= $this->_renderAlertType(anvilAlert::TYPE_WARNING, 'warning');
        $html .= $this->_renderAlertType(anvilAlert::TYPE_SUCCESS, 'success');
        $html .= $this->_renderAlertType(anvilAlert::TYPE_INFO, 'info');

        return $html;
    }

}

?>