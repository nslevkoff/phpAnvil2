<?php
/**
 *
 * @file
 *                 Content Module Controller
 *
 * @author         Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright      (c) 2010 Solutions By Design
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        Content_Module
 *
 */
//require_once 'content.inc.php';

require_once PHPANVIL_FRAMEWORK_PATH . 'Base.module.php';

//---- Load Controls Include
require_once PHPANVIL_TOOLS_PATH . 'atPage.class.php';
//require_once(PHPANVIL_TOOLS_PATH . 'DevLiteral.class.php');

/**
 *
 * Content Module Class
 *
 * @author         Nick Slevkoff <nick.slevkoff@solutionsbydesign.com>
 * @copyright      (c) 2010 Solutions By Design
 * @license
 *                 This source file is subject to the new BSD license that is
 *                 bundled with this package in the file LICENSE.txt. It is also
 *                 available on the Internet at:  http://www.phpanvil.com/LICENSE.txt
 * @ingroup        Content_Module
 *
 */

class ContentModule extends BaseModule
{

    public $headEnd = '';
    public $bodyBegin = '';
    public $bodyEnd = '';
    public $html = '';

    public $atTemplate;
    public $page;

    public $webRootPath = '';


    function __construct()
    {
        global $options;

        $this->enableTrace();

        $return = parent::__construct();

        $this->type    = self::TYPE_CORE;
        $this->name    = 'Content Module';
        $this->refName = 'Content';
        $this->version = '1.0';
        $this->build   = '4';

        return $return;
    }


    function init()
    {
        $return = true;

        if (!$this->_isInitialized) {
            //---- Instantiate templatePage Object
            $this->page = new atPage('', '', null, true);
            //        $this->page->enableTrace();

            $return = parent::init();

        }

        return $return;
    }


    function open()
    {
        $return = true;

        if (!$this->_isOpened) {
            $return = parent::open();

            if (!is_object($this->page->atTemplate)) {
                $this->page->atTemplate = $this->atTemplate;
            }
        }

        return $return;
    }


    public function assign($var, $value)
    {
        $this->page->atTemplate->assign($var, $value);
    }


    function parseCode($code, $value)
    {
        $this->html = str_ireplace($code, $value, $this->html);
    }


    function processAction(Action $action)
    {
        global $modules, $phpAnvil;
        global $options;
        global $firePHP;

        switch ($action->type) {
            CASE ACTION_DISPLAY_CONTENT:
//				FB::log('[CONTENT] Processing ACTION_DISPLAY_CONTENT...');

                $this->display();
                break;

//			CASE ACTION_PROMO_RESPONSE_PHASE:
//				FB::log('[CONTENT] Processing ACTION_PROMO_RESPONSE_PHASE...');
//				break;

            CASE ACTION_PARSE_AND_DISPLAY_CONTENT:
//				FB::log('[CONTENT] Processing ACTION_PARSE_AND_DISPLAY_CONTENT...');

                $this->assign('appVersion', $options['app']['version']);
                $this->assign('appBuild', $options['app']['build']);

                $this->assign('webRootPath', $phpAnvil->webRootPath);
                $this->assign('faviconPath', $phpAnvil->webRootPath . 'favicon.ico');

                if (is_object($modules[MODULE_PROMO]->promotion)) {
                    $this->assign('promoURLCode', $modules[MODULE_PROMO]->promotion->urlCode);
                }
                if (is_object($modules[MODULE_PROMO]->step)) {
                    $this->assign('promoStepURLCode', $modules[MODULE_PROMO]->step->urlCode);
                }

                $this->html = $this->page->render();

                if (!empty($this->headEnd)) {
                    $this->html = str_ireplace('</head>', $this->headEnd . '</head>', $this->html);
                }

                if (!empty($this->bodyBegin)) {
                    $this->html = str_ireplace('<body>', '<body>' . $this->bodyBegin, $this->html);
                }

                if (!empty($this->bodyEnd)) {
                    $this->html = str_ireplace('</body>', $this->bodyEnd . '</body>', $this->html);
                }

                echo $this->html;

                break;

        }
    }


    function display()
    {
        global $phpAnvil;
        //		global $firePHP;

        $this->assign('applicationName', $phpAnvil->application->name);

        //        fb::log($phpAnvil->application->name, '$phpAnvil->application->name');

        $this->assign('applicationRefName', $phpAnvil->application->refName);
        $this->assign('applicationVersion', $phpAnvil->application->version);
        $this->assign('applicationBuild', $phpAnvil->application->build);
        $this->assign('applicationCopyright', $phpAnvil->application->copyright);

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
//        $phpAnvil->module['content']->page->addControl(new atLiteral('preHEAD', $this->preHEAD));
//        $phpAnvil->module['content']->page->addControl(new atLiteral('preScript', $this->preScript));

//        $headTokens = array(
//            'title'         => $phpAnvil->application->name,
//            'base'          => $phpAnvil->site->webPath,
//            'contentType'   => $phpAnvil->application->version,
//            'build'         => $phpAnvil->application->build,
//            'copyright'     => $phpAnvil->application->copyright,
//            'copyrightHTML' => $phpAnvil->application->copyrightHTML
//        );
//        $this->assign('head', $headTokens);

        //        $firePHP->log($this->preHEAD);

//        if (!empty($this->_headScripts)) {
//            $this->assign('headScripts', $this->_headScripts);
//        }
//        if (!empty($this->_headStyles)) {
//            $this->assign('headStyles', $this->_headStyles);
//        }
//        if (!empty($this->_headStylesheets)) {
//            $this->assign('headStylesheets', $this->_headStylesheets);
//        }


        //		FB::log('$phpAnvil->site->webPath=' . $phpAnvil->site->webPath);
        //		FB::log('$phpAnvil->webRootPath=' . $phpAnvil->webRootPath);

        $this->assign('webPath', $phpAnvil->site->webPath);
        //		$this->assign('favicon', $phpAnvil->site->webPath . 'favicon.ico');
        //		$this->assign('faviconPath', $phpAnvil->webRootPath . 'favicon.ico');


        $this->page->display();
    }

}

$phpAnvil->module['content'] = new ContentModule();

?>
