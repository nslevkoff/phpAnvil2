<?php
require_once APP_PATH . 'models/bpbatch.model.php';
require_once APP_PATH . 'models/bptask.model.php';

require_once(PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php');

abstract class anvilBPAbstract extends anvilObjectAbstract
{

    public $name = 'New Background Process';
    public $version = '0.1';
    public $build = '1';
    public $copyright = '(c) 2012';

    /**
     * @var anvilApplicationAbstract
     */
    protected $_application;

    /**
     * @var phpAnvil2
     */
    protected $_core;

    /** @var bpBatchModel */
    protected $_batch;

    /**
     * @var anvilSiteAbstract
     */
    protected $_site;

    /** @var bpTaskmodel */
    protected $_task;


	function __construct($batch = null, $task = null)
    {
        global $phpAnvil;

        $this->_core        = $phpAnvil;
        $this->_application = $phpAnvil->application;
        $this->_site        = $phpAnvil->site;

        if ($batch) {
            $this->_batch = $batch;
        }

        if ($task) {
            $this->_task = $task;
        }

        //---- Default Timeout to 5 minutes
        set_time_limit(300);

        return true;
	}


    function init()
    {
//        global $phpAnvil;

        $return = true;

        return $return;
    }


    function process()
    {
        global $phpAnvil;

        $return = false;

        $phpAnvil->triggerEvent('application.open');


        //---- Check if Site is Set
        if (isset($phpAnvil->site))
        {
            //---- Initialize the Site
            $phpAnvil->site->open();
            $return = true;
        } else {
            FB::error('Site not set in phpAnvil.');
        }

        return $return;
    }


    function close()
    {
        $return = true;

        return $return;
    }
}

?>