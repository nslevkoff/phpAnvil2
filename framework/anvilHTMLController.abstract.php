<?php

require_once 'anvilController.abstract.php';
//require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilResponse/anvilHTMLResponse.class.php';


abstract class anvilHTMLControllerAbstract extends anvilControllerAbstract
{

//    public $templateFilename = 'default.tpl';
//    private $_content;


	function __construct()
    {
        global $phpAnvil;

        parent::__construct();

        $this->enableLog();

        $this->response = new anvilHTMLResponse();
        $this->response->template = $phpAnvil->application->newTemplate();
//        $this->_content = new anvilContainer('content');

		return true;
	}


    function init()
    {
//        global $phpAnvil;

        $return = parent::init();

        if ($return) {

//            $this->page = new anvilPage('', '', null, true);
        }

        return $return;
    }


    function open()
    {
        global $phpAnvil;

        $return = parent::open();

        if ($return) {
//            if (!is_object($this->page->template)) {
//                $this->page->anvilTemplate = $this->template;
//                $this->page->template = $phpAnvil->application->newTemplate();
//                $this->page->template = $phpAnvil->application->newTemplate();
//            }
        }

        return $return;
    }


    public function addControl($control)
    {
        $this->response->addControl($control);
    }

//    public function addContentControl($control)
//    {
//        $this->_content->addControl($control);
//    }

    public function assign($var, $value)
    {
        $this->response->template->assign($var, $value);
    }

    function display()
    {
        global $phpAnvil;

//        $this->response->innerTemplate = $this->templateFilename;

//        $this->assign('applicationName', $phpAnvil->application->name);
//        $this->assign('applicationRefName', $phpAnvil->application->refName);
//        $this->assign('applicationVersion', $phpAnvil->application->version);
//        $this->assign('applicationBuild', $phpAnvil->application->build);
//        $this->assign('applicationCopyright', $phpAnvil->application->copyright);

//        $this->addControl($this->_content);

        $appTokens = array(
            'name'          => $phpAnvil->application->name,
            'refName'       => $phpAnvil->application->refName,
            'version'       => $phpAnvil->application->version,
            'build'         => $phpAnvil->application->build,
            'copyright'     => $phpAnvil->application->copyright,
            'copyrightHTML' => $phpAnvil->application->copyrightHTML
        );
        $this->assign('app', $appTokens);


        //---- HEAD ------------------------------------------------------------
        $this->assign('webPath', $phpAnvil->site->webPath);

        $alerts = $this->renderAlerts();
//        $this->_logDebug($alerts, '$alerts');
        $this->assign('alerts', $alerts);


        return $this->response->display();
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

    public function renderAlerts()
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