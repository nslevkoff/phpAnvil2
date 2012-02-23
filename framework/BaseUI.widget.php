<?php

require_once PHPANVIL2_COMPONENT_PATH . 'anvilContainer.class.php';
require_once PHPANVIL2_COMPONENT_PATH . 'anvilLink.class.php';

require_once 'Base.widget.php';


class BaseUIWidget extends BaseWidget {

    private $_breadCrumbs = array();
    public $breadcrumbSeparator = '  ::  ';

    private $_headStylesheets = '';
    private $_headScripts = '';
    private $_headStyles = '';

    private $_body = array();

    public $content;
    public $contentModule;
    public $icon;
    public $page;
    public $template = 'default.tpl';


	function __construct($id = '')
    {
        global $phpAnvil;

		parent::__construct($id);

        $this->name = 'New UI Widget';
        $this->version = '1.0';
        $this->build = '1';

        $this->contentModule = $phpAnvil->module['content'];
        $this->page = $this->contentModule->page;
        $this->content = new anvilContainer('content');

		return true;
	}

    public function addBodyBottomHTML($html)
    {
        $this->_body['bottom'] .= $html;
    }

    public function addBodyTopHTML($html)
    {
        $this->_body['top'] .= $html;
    }

    public function addBreadCrumb($name, $url)
    {
        $this->_breadCrumbs[] = new anvilLink('', $name, $url);
    }


    public function addControl($control)
    {
        return $this->content->addControl($control);
    }


    public function addScript($source, $script = '', $type = 'text/javascript')
    {
        $this->page->head->addScript($source, $script, $type);
    }


    public function addStyle($style)
    {
        $this->page->head->addStyle($style);
    }

    public function addStylesheet($stylesheet, $media = '')
    {
        $this->page->head->addStylesheet($stylesheet, $media);
    }

    public function assign($name, $value)
    {
        $this->contentModule->assign($name, $value);
    }

    public function display()
    {
        global $phpAnvil;

        //---- HEAD ------------------------------------------------------------
        $this->page->head->title = $phpAnvil->application->name;
        $this->page->head->base = $phpAnvil->site->webPath;

        if (!empty($this->icon)) {
            $this->page->head->icon = $phpAnvil->site->webPath . 'ANDI.ico';
        }

        $this->page->innerTemplate = $this->template;
        $this->page->addControl($this);

        $this->page->addControl($this->content);

//        $phpAnvil->module['content']->page->addControl($phpAnvil->pageMsg);
//        $phpAnvil->module['content']->page->addControl($phpAnvil->errorMsg);
//        $phpAnvil->module['content']->page->addControl($phpAnvil->actionMsg);

        $this->contentModule->display();
    }


    public function renderContent()
    {
        //---- HEAD ------------------------------------------------------------
        if (!empty($this->_headScripts)) {
            $this->assign('headScripts', $this->_headScripts);
        }
        if (!empty($this->_headStyles)) {
            $this->assign('headStyles', $this->_headStyles);
        }
        if (!empty($this->_headStylesheets)) {
            $this->assign('headStylesheets', $this->_headStylesheets);
        }

        $this->assign('body', $this->_body);

        //---- Bread Crumbs ----------------------------------------------------
        $maxBreadCrumbs  = count($this->_breadCrumbs);
        $breadCrumbsHTML = '';

        for ($i = 0; $i < $maxBreadCrumbs; $i++) {
            $breadCrumbsHTML .= $this->_breadCrumbs[$i]->render();
            $breadCrumbsHTML .= $this->breadcrumbSeparator;
        }

        $this->assign('breadcrumbs', $breadCrumbsHTML);

        $return = parent::renderContent();

        return $return;
    }
}

?>